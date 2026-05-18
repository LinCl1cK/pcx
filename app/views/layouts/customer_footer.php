<footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-4 col-md-6">
                <h5 class="text-uppercase fw-bold mb-3">PCX Store</h5>
                <p class="text-secondary small pe-lg-4">
                    Your premier destination for high-performance computer hardware, laptops, and networking components. 
                    Build your dream setup with top-tier brands and expert support.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-light fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-light fs-5"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-light fs-5"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3">Shop</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/?r=catalog/product/list" class="text-secondary text-decoration-none hover-white">All Products</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-white">PC Components</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-white">Networking</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-white">New Arrivals</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3">Support</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/?r=order/order/track" class="text-secondary text-decoration-none hover-white">Track Order</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/?r=service/customerTicket/request" class="text-secondary text-decoration-none hover-white">Service Request</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-white">Return Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-white">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3">Account Area</h6>
                <p class="text-secondary small mb-3">Manage your orders, wishlist, and service tickets.</p>
                
                <?php if (empty($_SESSION['user'])): ?>
                    <button type="button" class="btn btn-outline-light btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#authModal">
                        Customer Login / Register
                    </button>
                    <a href="#" class="text-secondary small text-decoration-none d-block text-center mt-2" onclick="document.getElementById('emp-tab-btn').click();" data-bs-toggle="modal" data-bs-target="#authModal">
                        <i class="bi bi-shield-lock me-1"></i>Staff Portal Access
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/?r=auth/auth/account" class="btn btn-outline-light btn-sm w-100 mb-2">My Account</a>
                    <a href="<?= BASE_URL ?>/?r=auth/auth/logout" class="btn btn-danger btn-sm w-100">Logout</a>
                <?php endif; ?>
            </div>

        </div>
        
        <hr class="border-secondary mt-4 mb-3">
        
        <div class="row align-items-center small text-secondary">
            <div class="col-md-6 text-center text-md-start">
                &copy; <?= date('Y') ?> PCX Store. All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                <a href="#" class="text-secondary text-decoration-none me-3">Privacy Policy</a>
                <a href="#" class="text-secondary text-decoration-none">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <ul class="nav nav-underline ms-auto w-100 d-flex justify-content-between" role="tablist" id="authTabList">
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link active fw-bold w-100 text-dark" id="login-tab-btn" data-bs-toggle="tab" data-bs-target="#loginTab" type="button" role="tab">LOGIN</button>
                    </li>
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link fw-bold w-100 text-dark" id="reg-tab-btn" data-bs-toggle="tab" data-bs-target="#regTab" type="button" role="tab">REGISTER</button>
                    </li>
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link fw-bold w-100 text-danger" id="emp-tab-btn" data-bs-toggle="tab" data-bs-target="#empTab" type="button" role="tab">STAFF</button>
                    </li>
                </ul>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 pt-3">
                <div class="tab-content" id="authTabContent">
                    
                    <div class="tab-pane fade show active" id="loginTab" role="tabpanel" tabindex="0">
                        <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/login">
                            <input type="hidden" name="login_type" value="customer">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Email address</label>
                                <input type="email" name="email" class="form-control form-control-lg bg-light" required placeholder="name@example.com">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg bg-light" required placeholder="••••••••">
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-2 fs-5 fw-semibold shadow-sm">Sign In</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="regTab" role="tabpanel" tabindex="0">
                        <form method="post" action="<?= BASE_URL ?>/?r=auth/auth/register">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control bg-light" required placeholder="John Doe">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Email address</label>
                                <input type="email" name="email" class="form-control bg-light" required placeholder="name@example.com">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control bg-light" required placeholder="Create a password">
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold shadow-sm">Create Account</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="empTab" role="tabpanel" tabindex="0">
                        <div id="employeeAlert" class="alert alert-danger d-none py-2 small"></div>
                        <form id="ajaxEmployeeForm" method="post" action="<?= BASE_URL ?>/?r=auth/auth/login">
                            <input type="hidden" name="login_type" value="employee">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Staff Username / Email</label>
                                <input type="text" name="login_id" class="form-control bg-light border-danger-subtle" required placeholder="Admin ID">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control bg-light border-danger-subtle" required placeholder="••••••••">
                            </div>
                            <button type="submit" id="empSubmitBtn" class="btn btn-danger w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-shield-lock me-2"></i>Access Staff Portal
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
  
<script>
    window.BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Optional Footer Custom CSS to add to your style.css */
footer a.hover-white:hover {
    color: #ffffff !important;
    text-decoration: underline !important;
}
</style>
</body>
</html>