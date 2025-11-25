<script>
    var hostUrl = "assets/";
</script>

<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('assets/backend/plugins/global/plugins.bundle') }}"></script>
<script src="{{ asset('assets/backend/js/scripts.bundle') }}"></script>
<!--end::Global Javascript Bundle-->

<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{ asset('assets/backend/plugins/custom/fullcalendar/fullcalendar.bundle') }}"></script>
<script src="{{ asset('assets/backend/plugins/custom/datatables/datatables.bundle') }}"></script>
<!--end::Page Vendors Javascript-->

<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{ asset('assets/backend/js/widgets.bundle') }}"></script>
<script src="{{ asset('assets/backend/js/custom/widgets') }}"></script>
<script src="{{ asset('assets/backend/js/custom/apps/chat/chat') }}"></script>
<script src="{{ asset('assets/backend/js/custom/utilities/modals/upgrade-plan') }}"></script>
<script src="{{ asset('assets/backend/js/custom/utilities/modals/create-app') }}"></script>
<script src="{{ asset('assets/backend/js/custom/utilities/modals/users-search') }}"></script>
<!--end::Page Custom Javascript-->

@stack('page_js')
