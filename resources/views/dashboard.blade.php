@extends('layouts.bootstrap')

@section('styles')
<style>
    .bristol-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }
    
    .bristol-radio {
        display: none;
    }

    .bristol-card {
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.02);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .bristol-card:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--poop-accent);
        transform: translateY(-2px);
    }

    .bristol-radio:checked + .bristol-card {
        background: var(--poop-accent-glow);
        border-color: var(--poop-accent);
        box-shadow: 0 4px 15px rgba(217, 119, 6, 0.25);
    }

    .bristol-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .bristol-radio:checked + .bristol-card .bristol-number {
        background: var(--poop-accent);
        color: #fff;
    }

    .bristol-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .bristol-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.3;
    }

    .stat-badge {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .meal-selector-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--card-border);
        color: var(--text-muted);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .meal-selector-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-main);
    }

    .meal-selector-btn.active {
        background: var(--primary-accent-glow);
        border-color: var(--primary-accent);
        color: var(--text-main);
    }

    .icon-box-meal {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--primary-accent-glow);
        color: var(--primary-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-box-poop {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--poop-accent-glow);
        color: var(--poop-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <!-- Welcome Header & Daily Status -->
    <div class="col-12">
        <div class="glass-card p-4 d-md-flex align-items-center justify-content-between">
            <div>
                <h1 class="fw-bold mb-1" style="font-size: 2rem;">Jambo, {{ auth()->user()->name }}!</h1>
                <p class="text-muted mb-0">It's {{ $now->format('l, F j, Y - g:i A') }} in Nairobi. Keep tracking your metrics to monitor health trends.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <div class="stat-badge">
                    <i class="bi bi-egg-fried text-primary" style="color: var(--primary-accent) !important;"></i>
                    <span>Meals Today: <strong>{{ $todayMeals->count() }}</strong></span>
                </div>
                <div class="stat-badge">
                    <i class="bi bi-trash3 text-warning" style="color: var(--poop-accent) !important;"></i>
                    <span>Poops Today: <strong>{{ $todayPoops->count() }}</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Dynamic Meal prompt column -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="icon-box-meal">
                    @if($suggestedMeal === 'breakfast')
                        <i class="bi bi-brightness-alt-high-fill"></i>
                    @elseif($suggestedMeal === 'lunch')
                        <i class="bi bi-sun-fill"></i>
                    @elseif($suggestedMeal === 'dinner')
                        <i class="bi bi-moon-stars-fill"></i>
                    @else
                        <i class="bi bi-cup-hot-fill"></i>
                    @endif
                </div>
                <div>
                    <h3 class="fw-bold mb-0">Log Food Intake</h3>
                    <p class="text-muted mb-0">
                        Suggested current meal: <span class="badge text-uppercase bg-indigo" style="background-color: var(--primary-accent);">{{ $suggestedMeal }}</span>
                    </p>
                </div>
            </div>

            <form action="{{ route('meals.store') }}" method="POST">
                @csrf
                <!-- Meal Type Selector -->
                <div class="mb-4">
                    <label class="form-label-custom">Select Meal Type</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(['breakfast', 'lunch', 'dinner', 'snack'] as $type)
                            <button type="button" class="meal-selector-btn text-capitalize flex-fill {{ $suggestedMeal === $type ? 'active' : '' }}" onclick="selectMealType('{{ $type }}', this)">
                                @if($type === 'breakfast') <i class="bi bi-brightness-alt-high"></i>
                                @elseif($type === 'lunch') <i class="bi bi-sun"></i>
                                @elseif($type === 'dinner') <i class="bi bi-moon-stars"></i>
                                @else <i class="bi bi-cup-hot"></i>
                                @endif
                                {{ $type }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="meal_type" id="selected_meal_type" value="{{ $suggestedMeal }}">
                </div>

                <!-- Food Description -->
                <div class="mb-4">
                    <label for="food_description" class="form-label-custom">What did you eat?</label>
                    <textarea name="description" id="food_description" class="form-control form-control-custom" rows="4" placeholder="e.g. Rice, beans and stewed beef with a glass of water..." required></textarea>
                </div>

                <!-- Log Date and Time -->
                <div class="mb-4">
                    <label for="eaten_at" class="form-label-custom">Date & Time Eaten (Nairobi Time)</label>
                    <input type="datetime-local" name="eaten_at" id="eaten_at" class="form-control form-control-custom" value="{{ $now->format('Y-m-d\TH:i') }}" required>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-check2-circle me-2"></i> Log Meal
                </button>
            </form>
        </div>
    </div>

    <!-- Bowel movement column -->
    <div class="col-lg-6">
        <div class="glass-card p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="icon-box-poop">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">Log Bowel Movement</h3>
                    <p class="text-muted mb-0">Select a stool type from the Bristol Stool Chart</p>
                </div>
            </div>

            <form action="{{ route('bowel-movements.store') }}" method="POST">
                @csrf
                <!-- Bristol Stool Chart Grid -->
                <div class="mb-4">
                    <label class="form-label-custom d-block mb-3">Stool Type</label>
                    <div class="bristol-grid">
                        
                        <!-- Type 1 -->
                        <label>
                            <input type="radio" name="bristol_type" value="1" class="bristol-radio" required>
                            <div class="bristol-card">
                                <div class="bristol-number">1</div>
                                <div class="bristol-title text-warning">Separate Hard Lumps</div>
                                <div class="bristol-desc">Like nuts, hard to pass (Severe Constipation)</div>
                            </div>
                        </label>

                        <!-- Type 2 -->
                        <label>
                            <input type="radio" name="bristol_type" value="2" class="bristol-radio">
                            <div class="bristol-card">
                                <div class="bristol-number">2</div>
                                <div class="bristol-title text-warning">Lumpy Sausage</div>
                                <div class="bristol-desc">Sausage-shaped but lumpy (Mild Constipation)</div>
                            </div>
                        </label>

                        <!-- Type 3 -->
                        <label>
                            <input type="radio" name="bristol_type" value="3" class="bristol-radio">
                            <div class="bristol-card">
                                <div class="bristol-number">3</div>
                                <div class="bristol-title text-success">Cracked Sausage</div>
                                <div class="bristol-desc">Like a sausage with cracks on surface (Normal)</div>
                            </div>
                        </label>

                        <!-- Type 4 -->
                        <label>
                            <input type="radio" name="bristol_type" value="4" class="bristol-radio" checked>
                            <div class="bristol-card">
                                <div class="bristol-number">4</div>
                                <div class="bristol-title text-success">Smooth Snake</div>
                                <div class="bristol-desc">Like a sausage or snake, smooth and soft (Optimal)</div>
                            </div>
                        </label>

                        <!-- Type 5 -->
                        <label>
                            <input type="radio" name="bristol_type" value="5" class="bristol-radio">
                            <div class="bristol-card">
                                <div class="bristol-number">5</div>
                                <div class="bristol-title text-info">Soft Blobs</div>
                                <div class="bristol-desc">Soft blobs with clear-cut edges (Lacking Fiber)</div>
                            </div>
                        </label>

                        <!-- Type 6 -->
                        <label>
                            <input type="radio" name="bristol_type" value="6" class="bristol-radio">
                            <div class="bristol-card">
                                <div class="bristol-number">6</div>
                                <div class="bristol-title text-danger">Mushy Stool</div>
                                <div class="bristol-desc">Fluffy pieces with ragged edges, mushy (Mild Diarrhea)</div>
                            </div>
                        </label>

                        <!-- Type 7 -->
                        <label>
                            <input type="radio" name="bristol_type" value="7" class="bristol-radio">
                            <div class="bristol-card">
                                <div class="bristol-number">7</div>
                                <div class="bristol-title text-danger">Watery, No Solids</div>
                                <div class="bristol-desc">Watery, entirely liquid, no solid pieces (Diarrhea)</div>
                            </div>
                        </label>

                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label for="poop_notes" class="form-label-custom">Notes (Optional)</label>
                    <textarea name="notes" id="poop_notes" class="form-control form-control-custom" rows="3" placeholder="e.g. Easy to pass, slight stomach cramp beforehand..."></textarea>
                </div>

                <!-- Log Date and Time -->
                <div class="mb-4">
                    <label for="logged_at" class="form-label-custom">Date & Time Logged (Nairobi Time)</label>
                    <input type="datetime-local" name="logged_at" id="logged_at" class="form-control form-control-custom" value="{{ $now->format('Y-m-d\TH:i') }}" required>
                </div>

                <button type="submit" class="btn btn-poop-custom w-100">
                    <i class="bi bi-check2-circle me-2"></i> Log Bowel Movement
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Today's logs quick summary list -->
    <div class="col-12">
        <div class="glass-card p-4">
            <h3 class="fw-bold mb-3"><i class="bi bi-clock-history text-indigo" style="color: var(--primary-accent);"></i> Today's Summary Log</h3>
            <div class="row">
                <!-- Meals column -->
                <div class="col-md-6 mb-3 mb-md-0 border-end border-secondary border-opacity-25">
                    <h5 class="fw-semibold mb-3 text-indigo" style="color: var(--primary-accent);"><i class="bi bi-egg-fried"></i> Food Eaten Today</h5>
                    @if($todayMeals->isEmpty())
                        <p class="text-muted small">No meals logged for today yet. Make sure to track what you eat!</p>
                    @else
                        <div class="list-group list-group-flush bg-transparent">
                            @foreach($todayMeals as $meal)
                                <div class="list-group-item bg-transparent text-white border-0 border-bottom border-secondary border-opacity-10 py-2 px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge text-uppercase bg-indigo me-2" style="background-color: var(--primary-accent); font-size: 0.75rem;">{{ $meal->meal_type }}</span>
                                            <span class="fw-medium">{{ $meal->description }}</span>
                                        </div>
                                        <small class="text-muted">{{ $meal->eaten_at->format('g:i A') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Bowel movements column -->
                <div class="col-md-6">
                    <h5 class="fw-semibold mb-3 text-warning" style="color: var(--poop-accent);"><i class="bi bi-trash3"></i> Bowel Movements Today</h5>
                    @if($todayPoops->isEmpty())
                        <p class="text-muted small">No bowel movements logged for today yet.</p>
                    @else
                        <div class="list-group list-group-flush bg-transparent">
                            @foreach($todayPoops as $poop)
                                <div class="list-group-item bg-transparent text-white border-0 border-bottom border-secondary border-opacity-10 py-2 px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge bg-warning text-dark me-2" style="background-color: var(--poop-accent); color: #fff !important; font-size: 0.75rem;">Type {{ $poop->bristol_type }}</span>
                                            <span class="fw-medium text-muted italic">{{ $poop->notes ?: 'No notes entered' }}</span>
                                        </div>
                                        <small class="text-muted">{{ $poop->logged_at->format('g:i A') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectMealType(type, button) {
        // Set the hidden input value
        document.getElementById('selected_meal_type').value = type;

        // Toggle active classes on selector buttons
        const buttons = document.querySelectorAll('.meal-selector-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        // Dynamically adjust icon in the box for feedback
        const iconBox = document.querySelector('.icon-box-meal');
        if (type === 'breakfast') {
            iconBox.innerHTML = '<i class="bi bi-brightness-alt-high-fill"></i>';
        } else if (type === 'lunch') {
            iconBox.innerHTML = '<i class="bi bi-sun-fill"></i>';
        } else if (type === 'dinner') {
            iconBox.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        } else {
            iconBox.innerHTML = '<i class="bi bi-cup-hot-fill"></i>';
        }
    }
</script>
@endsection
