@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">طلبات الموافقة على المستخدمين</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>معلومات الاتصال</th>
                                    <th>المعلومات الشخصية</th>
                                    <th>المؤهلات والخبرات</th>
                                    <th>المعلومات البنكية</th>
                                    <th>المرفقات</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <div><strong>البريد:</strong> {{ $user->email }}</div>
                                            <div><strong>الجوال:</strong> {{ optional($user->profile)->phone ?? 'غير محدد' }}</div>
                                            <div><strong>العنوان:</strong> {{ optional($user->profile)->address ?? 'غير محدد' }}</div>
                                        </td>
                                        <td>
                                            <div><strong>رقم الهوية:</strong> {{ optional($user->profile)->national_id ?? 'غير محدد' }}</div>
                                            <div><strong>تاريخ الميلاد:</strong> {{ optional($user->profile)->date_of_birth ? optional($user->profile)->date_of_birth->format('Y-m-d') : 'غير محدد' }}</div>
                                        </td>
                                        <td>
                                            <div><strong>المؤهل:</strong> {{ optional($user->profile)->qualification ?? 'غير محدد' }}</div>
                                            <div><strong>الخبرات الأكاديمية:</strong> {{ optional($user->profile)->academic_experience ?? 'غير محدد' }}</div>
                                        </td>
                                        <td>
                                            <div><strong>رقم الآيبان:</strong> {{ optional($user->profile)->iban_number ?? 'غير محدد' }}</div>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm">
                                                @if($user->profile && $user->profile->cv_path)
                                                    <a href="{{ optional($user->profile)->cv_url }}" class="btn btn-info btn-sm mb-1" target="_blank">
                                                        <i class="fas fa-file-pdf"></i> السيرة الذاتية
                                                    </a>
                                                @endif
                                                @if($user->profile && $user->profile->iban_attachment)
                                                    <a href="{{ optional($user->profile)->iban_attachment_url }}" class="btn btn-info btn-sm mb-1" target="_blank">
                                                        <i class="fas fa-file"></i> مرفق الآيبان
                                                    </a>
                                                @endif
                                                @if($user->profile && $user->profile->national_address_attachment)
                                                    <a href="{{ optional($user->profile)->national_address_attachment_url }}" class="btn btn-info btn-sm mb-1" target="_blank">
                                                        <i class="fas fa-file"></i> العنوان الوطني
                                                    </a>
                                                @endif
                                                @if($user->profile && $user->profile->national_id_attachment)
                                                    <a href="{{ optional($user->profile)->national_id_attachment_url }}" class="btn btn-info btn-sm mb-1" target="_blank">
                                                        <i class="fas fa-file"></i> الهوية الوطنية
                                                    </a>
                                                @endif
                                                @if($user->profile && $user->profile->experience_certificate)
                                                    <a href="{{ optional($user->profile)->experience_certificate_url }}" class="btn btn-info btn-sm mb-1" target="_blank">
                                                        <i class="fas fa-file"></i> شهادة الخبرة
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success btn-sm" 
                                                        onclick="approveUser({{ $user->id }})">
                                                    <i class="fas fa-check"></i> موافقة
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="showRejectModal({{ $user->id }})">
                                                    <i class="fas fa-times"></i> رفض
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا يوجد طلبات موافقة معلقة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $pendingUsers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Rejection -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رفض المستخدم</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">سبب الرفض</label>
                        <textarea class="form-control" id="rejection_reason" 
                                name="rejection_reason" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.table td {
    vertical-align: middle;
}
.btn-group-vertical .btn {
    text-align: right;
}
.btn-group-vertical .btn i {
    margin-left: 5px;
}
</style>
@endpush

@push('scripts')
<script>
function approveUser(userId) {
    if (confirm('هل أنت متأكد من الموافقة على هذا المستخدم؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/approve`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}

function showRejectModal(userId) {
    const modal = $('#rejectModal');
    const form = $('#rejectForm');
    form.attr('action', `/admin/users/${userId}/reject`);
    modal.modal('show');
}
</script>
@endpush 