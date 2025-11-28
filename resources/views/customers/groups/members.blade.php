@extends('app')

@section('title', 'Manage Group Members')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers / Groups /</span> Manage Members</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Group: {{ $customerGroup->group_name }}</h5>
            <a href="{{ route('customer-groups.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('customer-groups.update-members', $customerGroup->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Select Customers</label>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select-all" />
                                    </th>
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                                   name="customer_ids[]" 
                                                   value="{{ $customer->id }}"
                                                   class="customer-checkbox"
                                                   {{ in_array($customer->id, $groupCustomers) ? 'checked' : '' }} />
                                        </td>
                                        <td>{{ $customer->full_name }}</td>
                                        <td>{{ $customer->mobile_primary }}</td>
                                        <td>{{ $customer->email ?: '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $customer->customer_type }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Members</button>
            </form>
        </div>
    </div>
</div>

@push('page_js')
<script>
    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all checkbox state
    document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(document.querySelectorAll('.customer-checkbox')).every(cb => cb.checked);
            const noneChecked = Array.from(document.querySelectorAll('.customer-checkbox')).every(cb => !cb.checked);
            document.getElementById('select-all').checked = allChecked;
            document.getElementById('select-all').indeterminate = !allChecked && !noneChecked;
        });
    });
</script>
@endpush
@endsection

