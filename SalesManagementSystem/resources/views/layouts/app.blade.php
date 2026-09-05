<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Dashboard') — Veytrix</title>
    <meta name="description" content="Veytrix — Enterprise Customer Relationship & Workflow Management System">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- CRM360 Custom CSS -->
    <link href="{{ asset('css/crm360.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="crm-body">

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ─── Sidebar ──────────────────────────────────────────────────────── -->
    @include('components.sidebar')

    <!-- ─── Main Wrapper ─────────────────────────────────────────────────── -->
    <div class="main-wrapper" id="mainWrapper">

        <!-- Top Navigation -->
        @include('components.topnav')

        <!-- Page Content -->
        <main class="main-content">
            <!-- Breadcrumb -->
            @hasSection('breadcrumb')
            <div class="breadcrumb-bar">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            @endif

            <!-- Flash Messages -->
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

            <!-- Page Body -->
            <div class="content-body">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="crm-footer">
            <span>CRM360 &copy; {{ date('Y') }} — Enterprise CRM System</span>
            <span class="ms-auto">Industrial Modern Theme v2.0</span>
        </footer>
    </div>

    <!-- Floating Action / Back to Top Button (As in screenshot) -->
    <button class="crm-floating-action-btn" id="scrollTopBtn" title="Scroll to Top">
        <i class="fas fa-angle-up"></i>
    </button>

    <!-- ─── Scripts ──────────────────────────────────────────────────────── -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <!-- Global Setup & Theme Controls -->
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Toggle Sidebar (both sidebar button and topnav button)
        function toggleSidebar() {
            document.getElementById('mainWrapper').classList.toggle('sidebar-collapsed');
            document.getElementById('crm-sidebar').classList.toggle('collapsed');
        }
        document.getElementById('sidebarToggle')?.addEventListener('click', toggleSidebar);
        document.getElementById('sidebarToggleTop')?.addEventListener('click', toggleSidebar);

        // Mobile Sidebar Overlay
        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            document.getElementById('crm-sidebar').classList.remove('show');
            this.classList.remove('active');
        });
        document.getElementById('mobileSidebarToggle')?.addEventListener('click', function() {
            document.getElementById('crm-sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        });

        // Fullscreen Toggle
        document.getElementById('fullscreenToggle')?.addEventListener('click', function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
                this.innerHTML = '<i class="fas fa-compress"></i>';
            } else {
                document.exitFullscreen().catch(err => console.log(err));
                this.innerHTML = '<i class="fas fa-expand"></i>';
            }
        });

        // Floating Back to Top Button
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 200) {
                scrollTopBtn?.classList.add('visible');
            } else {
                scrollTopBtn?.classList.remove('visible');
            }
        });
        scrollTopBtn?.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Quick Global Search Listener
        document.getElementById('globalCrmSearch')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let query = this.value.trim();
                if (query.length > 0) {
                    // Filter in table if present, or search leads/customers
                    let dt = $('.dataTable').DataTable();
                    if (dt) {
                        dt.search(query).draw();
                    } else {
                        window.location.href = "{{ route('customers.index') }}?search=" + encodeURIComponent(query);
                    }
                }
            }
        });

        // Auto-dismiss alerts after 5s
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                let alert = bootstrap.Alert.getOrCreateInstance(el);
                alert.close();
            });
        }, 5000);

        // Init Select2 & Pickers
        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

            document.querySelectorAll('.datepicker').forEach(el => {
                flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true });
            });
            document.querySelectorAll('.datetimepicker').forEach(el => {
                flatpickr(el, { dateFormat: 'Y-m-d H:i', enableTime: true, allowInput: true });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
