@extends('layouts.app')

@section('content')
<div class="admin-layout">
    <!-- القائمة الجانبية المتطورة -->
    @include('admin.layouts.sidebar')

    <!-- المحتوى الرئيسي -->
    <div class="main-content" id="mainContent">
        <div class="content-wrapper">
            @yield('admin_content')
        </div>
    </div>
</div>

<style>
/* تخطيط الإدارة الرئيسي */
.admin-layout {
    display: flex;
    min-height: calc(100vh - 60px);
    background: #f8fafc;
    position: relative;
}

/* المحتوى الرئيسي */
.main-content {
    flex: 1;
    margin-left: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: calc(100vh - 60px);
    display: flex;
    flex-direction: column;
}

.content-wrapper {
    flex: 1;
    padding: 2rem;
    overflow-x: auto;
}

/* تأثيرات الحركة للمحتوى */
.main-content {
    animation: fadeInRight 0.5s ease-out;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* استجابة للأجهزة المحمولة */
@media (max-width: 991.98px) {
    .admin-layout {
        flex-direction: column;
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .content-wrapper {
        padding: 1rem;
        padding-top: 80px; /* مساحة لزر التبديل المحمول */
    }
}

/* تحسينات للشاشات الصغيرة */
@media (max-width: 575.98px) {
    .content-wrapper {
        padding: 0.75rem;
        padding-top: 80px;
    }
}

/* تأثيرات إضافية للمحتوى */
.content-wrapper > * {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* تحسين الأداء */
.main-content {
    will-change: margin-left;
}

/* تخصيص scrollbar للمحتوى */
.content-wrapper::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.content-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.content-wrapper::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
}

.content-wrapper::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث margin للمحتوى الرئيسي عند تغيير حالة السايد بار
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebar && mainContent) {
        // تحديث initial state
        updateMainContentMargin();
        
        // مراقبة تغييرات السايد بار
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    updateMainContentMargin();
                }
            });
        });
        
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
        
        function updateMainContentMargin() {
            // للشاشات الكبيرة فقط
            if (window.innerWidth >= 992) {
                if (sidebar.classList.contains('collapsed')) {
                    mainContent.style.marginLeft = '0';
                } else {
                    mainContent.style.marginLeft = '0';
                }
            } else {
                mainContent.style.marginLeft = '0';
            }
        }
        
        // تحديث عند تغيير حجم الشاشة
        window.addEventListener('resize', updateMainContentMargin);
    }
    
    // تأثير smooth loading للصفحات
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        // إضافة تأثير loading عند تحميل محتوى جديد
        const originalContent = contentWrapper.innerHTML;
        
        // مراقبة تغييرات المحتوى
        const contentObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // إضافة تأثير fade in للعناصر الجديدة
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            node.style.opacity = '0';
                            node.style.transform = 'translateY(20px)';
                            
                            setTimeout(() => {
                                node.style.transition = 'all 0.3s ease';
                                node.style.opacity = '1';
                                node.style.transform = 'translateY(0)';
                            }, 10);
                        }
                    });
                }
            });
        });
        
        contentObserver.observe(contentWrapper, {
            childList: true,
            subtree: true
        });
    }
});
</script>
@endsection 