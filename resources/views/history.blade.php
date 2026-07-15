@extends('layouts.bootstrap')

@section('styles')
<style>
    .timeline-container {
        position: relative;
        padding-left: 2rem;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: rgba(255, 255, 255, 0.08);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-dot {
        position: absolute;
        left: -2rem;
        top: 6px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid var(--bg-color);
        z-index: 2;
    }
    .timeline-dot.meal-dot {
        background: var(--primary-accent);
        box-shadow: 0 0 8px var(--primary-accent);
    }
    .timeline-dot.poop-dot {
        background: var(--poop-accent);
        box-shadow: 0 0 8px var(--poop-accent);
    }
    .timeline-time {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .timeline-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }
    .timeline-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.15);
    }
    .filter-btn-group {
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 0.25rem;
        background: rgba(255, 255, 255, 0.02);
        display: inline-flex;
    }
    .filter-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 0.5rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .filter-btn:hover {
        color: var(--text-main);
    }
    .filter-btn.active {
        background: var(--primary-accent-glow);
        color: var(--text-main);
        border: 1px solid var(--card-border);
    }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <h1 class="fw-bold mb-1"><i class="bi bi-calendar2-week-fill text-indigo" style="color: var(--primary-accent);"></i> Health Log History</h1>
            <p class="text-muted mb-0">Browse, filter, and review your historical food intake and bowel movements.</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="glass-card p-4">
            <form action="{{ route('history') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <!-- Type Filter -->
                    <div class="col-md-5">
                        <label class="form-label-custom d-block">Log Type</label>
                        <div class="filter-btn-group w-100">
                            <button type="button" class="filter-btn flex-fill {{ $typeFilter === 'all' ? 'active' : '' }}" onclick="setTypeFilter('all')">
                                <i class="bi bi-collection-fill me-1"></i> All Logs
                            </button>
                            <button type="button" class="filter-btn flex-fill {{ $typeFilter === 'meals' ? 'active' : '' }}" onclick="setTypeFilter('meals')">
                                <i class="bi bi-egg-fried me-1"></i> Meals
                            </button>
                            <button type="button" class="filter-btn flex-fill {{ $typeFilter === 'poops' ? 'active' : '' }}" onclick="setTypeFilter('poops')">
                                <i class="bi bi-trash3 me-1"></i> Poops
                            </button>
                        </div>
                        <input type="hidden" name="type" id="typeFilterInput" value="{{ $typeFilter }}">
                    </div>

                    <!-- Date range -->
                    <div class="col-md-3">
                        <label for="start_date" class="form-label-custom">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-custom" value="{{ $startDate }}" onchange="document.getElementById('filterForm').submit()">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label-custom">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-custom" value="{{ $endDate }}" onchange="document.getElementById('filterForm').submit()">
                    </div>
                    
                    <!-- Reset Button -->
                    <div class="col-md-1">
                        <a href="{{ route('history') }}" class="btn btn-outline-secondary w-100 py-2 border-secondary border-opacity-25" style="border-radius: 10px;" title="Reset filters">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="row g-4">
    <div class="col-lg-8 mx-auto">
        <div class="glass-card p-4">
            <h3 class="fw-bold mb-4">Activity Timeline</h3>

            @if($timeline->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                    <p class="text-muted mt-3 mb-0">No logs matching the filters found.</p>
                </div>
            @else
                <div class="timeline-container">
                    @foreach($timeline as $item)
                        <div class="timeline-item">
                            <!-- Dot -->
                            @if($item['log_type'] === 'meal')
                                <div class="timeline-dot meal-dot" title="Meal"></div>
                            @else
                                <div class="timeline-dot poop-dot" title="Bowel Movement"></div>
                            @endif

                            <!-- Timestamp -->
                            <div class="timeline-time">
                                <i class="bi bi-clock me-1"></i>
                                {{ $item['timestamp']->format('l, M j, Y \a\t g:i A') }}
                            </div>

                            <!-- Card -->
                            <div class="timeline-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                                        @if($item['log_type'] === 'meal')
                                            <i class="bi bi-egg-fried text-primary" style="color: var(--primary-accent) !important;"></i>
                                            <span>{{ $item['title'] }}</span>
                                        @else
                                            <i class="bi bi-activity text-warning" style="color: var(--poop-accent) !important;"></i>
                                            <span>Bowel Movement</span>
                                        @endif
                                    </h5>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item['log_type'] === 'meal')
                                            <span class="badge text-uppercase bg-indigo py-1 px-2" style="background-color: var(--primary-accent); font-size: 0.75rem;">Food</span>
                                        @else
                                            <span class="badge bg-warning text-dark py-1 px-2" style="background-color: var(--poop-accent); color: #fff !important; font-size: 0.75rem;">Poop</span>
                                        @endif

                                        <button class="btn btn-sm btn-link p-0 text-muted lh-1" onclick="openEditModal('{{ $item['log_type'] }}', '{{ $item['uuid'] }}', '{{ $item['meta'] }}', '{{ addslashes($item['description'] ?? '') }}', '{{ $item['timestamp']->timezone('Africa/Nairobi')->format('Y-m-d\TH:i') }}', '{{ $item['title'] }}')" title="Edit"><i class="bi bi-pencil-square fs-6"></i></button>

                                        <form action="{{ route($item['log_type'] === 'meal' ? 'meals.destroy' : 'bowel-movements.destroy', $item['uuid']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this log?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link p-0 text-danger lh-1" title="Delete"><i class="bi bi-trash fs-6"></i></button>
                                        </form>
                                    </div>
                                </div>

                                @if($item['log_type'] === 'meal')
                                    <p class="mb-0 text-white-50">{{ $item['description'] }}</p>
                                @else
                                    <div class="mb-2 text-warning fw-semibold" style="font-size: 0.9rem;">
                                        {{ $bristolDescriptions[$item['meta']] ?? 'Bristol Type ' . $item['meta'] }}
                                    </div>
                                    @if($item['description'])
                                        <p class="mb-0 text-white-50 italic"><i class="bi bi-chat-left-text me-1 text-muted"></i> "{{ $item['description'] }}"</p>
                                    @else
                                        <span class="text-muted small">No notes logged.</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Meal Modal -->
<div class="modal fade" id="editMealModal" tabindex="-1" aria-labelledby="editMealModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white border-secondary" style="border-radius: 16px; background-color: #1a1a1a !important; border: 1px solid rgba(255,255,255,0.08) !important;">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold" id="editMealModalLabel">Edit Meal Log</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMealForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_meal_type" class="form-label-custom">Meal Type</label>
                        <select name="meal_type" id="edit_meal_type" class="form-select form-control-custom" style="background-color: #222; border-color: rgba(255,255,255,0.1); color: #fff;">
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_meal_description" class="form-label-custom">What did you eat?</label>
                        <textarea name="description" id="edit_meal_description" class="form-control form-control-custom" rows="3" style="background-color: #222; border-color: rgba(255,255,255,0.1); color: #fff;" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_meal_eaten_at" class="form-label-custom">Eaten At</label>
                        <input type="datetime-local" name="eaten_at" id="edit_meal_eaten_at" class="form-control form-control-custom" style="background-color: #222; border-color: rgba(255,255,255,0.1); color: #fff;" required>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary border-secondary border-opacity-50" data-bs-dismiss="modal" style="color: #ccc;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--primary-accent); border: none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bowel Movement Modal -->
<div class="modal fade" id="editBowelMovementModal" tabindex="-1" aria-labelledby="editBowelMovementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white border-secondary" style="border-radius: 16px; background-color: #1a1a1a !important; border: 1px solid rgba(255,255,255,0.08) !important;">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold" id="editBowelMovementModalLabel">Edit Bowel Movement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBowelMovementForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_bristol_type" class="form-label-custom">Bristol Stool Type</label>
                        <select name="bristol_type" id="edit_bristol_type" class="form-select form-control-custom" style="background-color: #222; border-color: rgba(255,255,255,0.1); color: #fff;">
                            <option value="1">Type 1: Separate hard lumps (constipated)</option>
                            <option value="2">Type 2: Sausage-shaped but lumpy</option>
                            <option value="3">Type 3: Sausage-shaped with cracks</option>
                            <option value="4">Type 4: Smooth and soft (optimal)</option>
                            <option value="5">Type 5: Soft blobs with clear-cut edges</option>
                            <option value="6">Type 6: Fluffy pieces, mushy</option>
                            <option value="7">Type 7: Watery, entirely liquid (diarrhea)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label-custom">Notes / Symptoms</label>
                        <textarea name="notes" id="edit_notes" class="form-control form-control-custom" style="background-color: #222; border-color: rgba(255,255,255,0.1); color: #fff;" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_logged_at" class="form-label-custom">Logged At</label>
                        <input type="datetime-local" name="logged_at" id="edit_logged_at" class="form-control form-control-custom" style="background-color: #222; border-color: rgba(255,255,255,0.1); color: #fff;" required>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary border-secondary border-opacity-50" data-bs-dismiss="modal" style="color: #ccc;">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark" style="background-color: var(--poop-accent); border: none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setTypeFilter(type) {
        document.getElementById('typeFilterInput').value = type;
        document.getElementById('filterForm').submit();
    }

    function openEditModal(logType, uuid, meta, description, timestamp, title) {
        if (logType === 'meal') {
            const form = document.getElementById('editMealForm');
            form.action = `/meals/${uuid}/update`;
            document.getElementById('edit_meal_type').value = title.toLowerCase();
            document.getElementById('edit_meal_description').value = description;
            document.getElementById('edit_meal_eaten_at').value = timestamp;

            const modal = new bootstrap.Modal(document.getElementById('editMealModal'));
            modal.show();
        } else {
            const form = document.getElementById('editBowelMovementForm');
            form.action = `/bowel-movements/${uuid}/update`;
            document.getElementById('edit_bristol_type').value = meta;
            document.getElementById('edit_notes').value = description;
            document.getElementById('edit_logged_at').value = timestamp;

            const modal = new bootstrap.Modal(document.getElementById('editBowelMovementModal'));
            modal.show();
        }
    }
</script>
@endsection
