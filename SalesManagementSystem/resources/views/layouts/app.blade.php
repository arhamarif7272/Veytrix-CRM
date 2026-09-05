@extends('layouts.master')

@section('body-class', 'crm-body')

@section('layout-content')
<div class="sidebar-overlay" id="sidebarOverlay"></div>
@include('components.sidebar')
<div class="main-wrapper" id="mainWrapper">
    @include('components.topnav')
    <main class="main-content">
        @hasSection('breadcrumb')
        <div class="breadcrumb-bar">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mx-4 mt-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="content-body">
            @yield('content')
        </div>
    </main>
    <footer class="crm-footer">
        <span>Veytrix &copy; {{ date('Y') }} — Enterprise CRM System</span>
        <span class="ms-auto">Industrial Modern Theme v2.0</span>
    </footer>
</div>
<button class="crm-floating-action-btn" id="scrollTopBtn" title="Scroll to Top">
    <i class="fas fa-angle-up"></i>
</button>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    function toggleSidebar() {
        document.getElementById('mainWrapper').classList.toggle('sidebar-collapsed');
        document.getElementById('crm-sidebar').classList.toggle('collapsed');
    }
    document.getElementById('sidebarToggle')?.addEventListener('click', toggleSidebar);
    document.getElementById('sidebarToggleTop')?.addEventListener('click', toggleSidebar);
    document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
        document.getElementById('crm-sidebar').classList.remove('show');
        this.classList.remove('active');
    });
    document.getElementById('mobileSidebarToggle')?.addEventListener('click', function() {
        document.getElementById('crm-sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    });
    document.getElementById('fullscreenToggle')?.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => console.log(err));
            this.innerHTML = '<i class="fas fa-compress"></i>';
        } else {
            document.exitFullscreen().catch(err => console.log(err));
            this.innerHTML = '<i class="fas fa-expand"></i>';
        }
    });
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', function() {
        scrollTopBtn?.classList.toggle('visible', window.scrollY > 200);
    });
    scrollTopBtn?.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    document.getElementById('globalCrmSearch')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let query = this.value.trim();
            if (query.length > 0) {
                let dt = $('.dataTable').DataTable();
                if (dt) { dt.search(query).draw(); }
                else { window.location.href = "{{ route('customers.index') }}?search=" + encodeURIComponent(query); }
            }
        }
    });
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => bootstrap.Alert.getOrCreateInstance(el).close());
    }, 5000);
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
        document.querySelectorAll('.datepicker').forEach(el => flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true }));
        document.querySelectorAll('.datetimepicker').forEach(el => flatpickr(el, { dateFormat: 'Y-m-d H:i', enableTime: true, allowInput: true }));
    });
</script>
@endsection
