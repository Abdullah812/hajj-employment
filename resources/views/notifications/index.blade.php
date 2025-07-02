@extends('layouts.app')

@section('title', 'الإشعارات - شركة مناسك المشاعر')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-bell me-2 text-primary"></i>الإشعارات</h2>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i>تمييز الكل كمقروء
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshNotifications()">
                        <i class="fas fa-sync-alt me-1"></i>تحديث
                    </button>
                </div>
            </div>

            <!-- فلترة الإشعارات -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="filterStatus">
                                <option value="">جميع الإشعارات</option>
                                <option value="unread">غير مقروءة</option>
                                <option value="read">مقروءة</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="filterType">
                                <option value="">جميع الأنواع</option>
                                <option value="application_status">حالة الطلبات</option>
                                <option value="new_job">وظائف جديدة</option>
                                <option value="contract_signed">توقيع العقود</option>
                                <option value="message">رسائل</option>
                                <option value="system">النظام</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="search" class="form-control form-control-sm" id="searchNotifications" 
                                   placeholder="البحث في الإشعارات...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- قائمة الإشعارات -->
            <div id="notificationsContainer">
                @forelse($notifications as $notification)
                    <div class="card mb-3 notification-card {{ $notification->is_read ? 'read' : 'unread' }}" 
                         data-notification-id="{{ $notification->id }}"
                         data-type="{{ $notification->type }}"
                         data-read="{{ $notification->is_read ? 'true' : 'false' }}">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="notification-icon">
                                        <i class="{{ $notification->icon_class }} {{ $notification->color_class }} fa-lg"></i>
                                    </div>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="mb-0 {{ $notification->is_read ? 'text-muted' : '' }}">
                                            {{ $notification->title }}
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary ms-2">جديد</span>
                                            @endif
                                        </h5>
                                        <small class="text-muted">{{ $notification->time_ago }}</small>
                                    </div>
                                    
                                    <p class="mb-2 {{ $notification->is_read ? 'text-muted' : '' }}">
                                        {{ $notification->message }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   onclick="markAsRead({{ $notification->id }})">
                                                    <i class="fas fa-external-link-alt me-1"></i>عرض التفاصيل
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="toggleReadStatus({{ $notification->id }})"
                                                    title="{{ $notification->is_read ? 'تمييز كغير مقروء' : 'تمييز كمقروء' }}">
                                                <i class="fas {{ $notification->is_read ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteNotification({{ $notification->id }})"
                                                    title="حذف الإشعار">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">لا توجد إشعارات</h4>
                        <p class="text-muted">ستظهر الإشعارات هنا عند توفرها</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>إحصائيات الإشعارات</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ $stats['total'] }}</h3>
                                <small class="text-muted">المجموع</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-danger mb-1">{{ $stats['unread'] }}</h3>
                                <small class="text-muted">غير مقروءة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">{{ $stats['read'] }}</h3>
                                <small class="text-muted">مقروءة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-info mb-1">{{ $stats['today'] }}</h3>
                                <small class="text-muted">اليوم</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- اختصارات -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>إعدادات الإشعارات</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                        <label class="form-check-label" for="emailNotifications">
                            إشعارات الإيميل
                        </label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="soundNotifications" checked>
                        <label class="form-check-label" for="soundNotifications">
                            الإشعارات الصوتية
                        </label>
                    </div>
                    
                    <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="clearOldNotifications()">
                        <i class="fas fa-broom me-1"></i>مسح الإشعارات القديمة
                    </button>
                    
                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="clearAllNotifications()">
                        <i class="fas fa-trash-alt me-1"></i>مسح جميع الإشعارات
                    </button>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <!-- أدوات الإدارة -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>أدوات الإدارة</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="createTestNotification()">
                        <i class="fas fa-flask me-1"></i>إنشاء إشعار تجريبي
                    </button>
                    
                    <button type="button" class="btn btn-outline-warning btn-sm w-100" onclick="sendBroadcastMessage()">
                        <i class="fas fa-bullhorn me-1"></i>رسالة عامة
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.notification-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-card.unread {
    background-color: rgba(180, 126, 19, 0.03);
    border-left-color: var(--primary-gold);
}

.notification-card.read {
    opacity: 0.8;
}

.notification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(180, 126, 19, 0.1);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}
</style>

<script>
// تمييز الإشعار كمقروء
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (card) {
                card.classList.remove('unread');
                card.classList.add('read');
                card.setAttribute('data-read', 'true');
                
                // تحديث النص والأيقونات
                const badge = card.querySelector('.badge');
                if (badge) badge.remove();
                
                const title = card.querySelector('h5');
                if (title) title.classList.add('text-muted');
                
                const message = card.querySelector('p');
                if (message) message.classList.add('text-muted');
            }
        }
    })
    .catch(error => console.error('خطأ:', error));
}

// تبديل حالة القراءة
function toggleReadStatus(notificationId) {
    fetch(`/notifications/${notificationId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // إعادة تحميل الصفحة لتحديث المظهر
        }
    })
    .catch(error => console.error('خطأ:', error));
}

// حذف الإشعار
function deleteNotification(notificationId) {
    if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (card) {
                    card.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => card.remove(), 300);
                }
            }
        })
        .catch(error => console.error('خطأ:', error));
    }
}

// تمييز الكل كمقروء
function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('خطأ:', error));
}

// إنشاء إشعار تجريبي (للإدارة فقط)
function createTestNotification() {
    fetch('/notifications/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم إنشاء إشعار تجريبي');
            location.reload();
        }
    })
    .catch(error => console.error('خطأ:', error));
}

// إرسال رسالة عامة (للإدارة فقط)
function sendBroadcastMessage() {
    // إنشاء modal للإدخال المتقدم
    const modalHTML = `
        <div class="modal fade" id="broadcastModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إرسال رسالة عامة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="broadcastForm">
                            <div class="mb-3">
                                <label for="broadcastTitle" class="form-label">عنوان الرسالة</label>
                                <input type="text" class="form-control" id="broadcastTitle" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label for="broadcastMessage" class="form-label">نص الرسالة</label>
                                <textarea class="form-control" id="broadcastMessage" rows="4" required maxlength="1000"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="targetRole" class="form-label">الفئة المستهدفة</label>
                                <select class="form-select" id="targetRole">
                                    <option value="all">جميع المستخدمين</option>
                                    <option value="employee">الموظفين فقط</option>
                                    <option value="company">الشركات فقط</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-primary" onclick="submitBroadcast()">إرسال</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // إضافة المودال للصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // إظهار المودال
    const modal = new bootstrap.Modal(document.getElementById('broadcastModal'));
    modal.show();
    
    // حذف المودال عند الإغلاق
    document.getElementById('broadcastModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

function submitBroadcast() {
    const title = document.getElementById('broadcastTitle').value;
    const message = document.getElementById('broadcastMessage').value;
    const targetRole = document.getElementById('targetRole').value;
    
    if (!title || !message) {
        alert('يرجى تعبئة جميع الحقول المطلوبة');
        return;
    }
    
    fetch('/notifications/broadcast', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            title: title,
            message: message,
            target_role: targetRole
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // إغلاق المودال
            bootstrap.Modal.getInstance(document.getElementById('broadcastModal')).hide();
        } else {
            alert(data.error || 'حدث خطأ أثناء إرسال الرسالة');
        }
    })
    .catch(error => {
        console.error('خطأ:', error);
        alert('حدث خطأ أثناء إرسال الرسالة');
    });
}

// مسح الإشعارات القديمة
function clearOldNotifications() {
    const days = prompt('عدد الأيام (الافتراضي: 30):', '30');
    if (!days) return;
    
    if (!isNaN(days) && parseInt(days) > 0) {
        if (confirm(`هل أنت متأكد من حذف جميع الإشعارات المقروءة الأقدم من ${days} يوماً؟`)) {
            fetch('/notifications/clear-old', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ days: parseInt(days) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (data.deleted_count > 0) {
                        location.reload();
                    }
                } else {
                    alert(data.error || 'حدث خطأ أثناء حذف الإشعارات');
                }
            })
            .catch(error => {
                console.error('خطأ:', error);
                alert('حدث خطأ أثناء حذف الإشعارات');
            });
        }
    } else {
        alert('يرجى إدخال رقم صحيح');
    }
}

// مسح جميع الإشعارات
function clearAllNotifications() {
    if (confirm('هل أنت متأكد من حذف جميع إشعاراتك؟ هذا الإجراء لا يمكن التراجع عنه!')) {
        fetch('/notifications/clear-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.error || 'حدث خطأ أثناء حذف الإشعارات');
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ أثناء حذف الإشعارات');
        });
    }
}

// تحديث الإشعارات
function refreshNotifications() {
    location.reload();
}

// فلترة الإشعارات
document.addEventListener('DOMContentLoaded', function() {
    const filterStatus = document.getElementById('filterStatus');
    const filterType = document.getElementById('filterType');
    const searchInput = document.getElementById('searchNotifications');
    
    function filterNotifications() {
        const cards = document.querySelectorAll('.notification-card');
        const statusFilter = filterStatus.value;
        const typeFilter = filterType.value;
        const searchTerm = searchInput.value.toLowerCase();
        
        cards.forEach(card => {
            let show = true;
            
            // فلترة الحالة
            if (statusFilter) {
                const isRead = card.getAttribute('data-read') === 'true';
                if (statusFilter === 'read' && !isRead) show = false;
                if (statusFilter === 'unread' && isRead) show = false;
            }
            
            // فلترة النوع
            if (typeFilter) {
                const cardType = card.getAttribute('data-type');
                if (cardType !== typeFilter) show = false;
            }
            
            // البحث
            if (searchTerm) {
                const cardText = card.textContent.toLowerCase();
                if (!cardText.includes(searchTerm)) show = false;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    }
    
    filterStatus.addEventListener('change', filterNotifications);
    filterType.addEventListener('change', filterNotifications);
    searchInput.addEventListener('input', filterNotifications);
});

// Animation للحذف
document.head.insertAdjacentHTML('beforeend', `
<style>
@keyframes fadeOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(100%); }
}
</style>
`);
</script>
@endsection 