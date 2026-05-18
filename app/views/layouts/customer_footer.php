    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <ul class="nav nav-underline ms-auto" role="tablist" id="authTabList">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="login-tab-btn" data-bs-toggle="tab" data-bs-target="#loginTab" type="button" role="tab" aria-controls="loginTab" aria-selected="true">LOGIN</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="reg-tab-btn" data-bs-toggle="tab" data-bs-target="#regTab" type="button" role="tab" aria-controls="regTab" aria-selected="false">REGISTER</button>
                        </li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="tab-content">
                        
                        <div class="tab-pane fade show active" id="loginTab" role="tabpanel" aria-labelledby="login-tab-btn">
                            <div id="loginAlert" class="alert alert-danger d-none py-2 small"></div>
                            <form id="ajaxLoginForm" method="post" action="<?= BASE_URL ?>/?r=auth/auth/login">
                                <input type="hidden" name="next" value="<?= htmlspecialchars($_GET['next'] ?? 'catalog/product/home') ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Email / Username</label>
                                    <input type="text" name="login_id" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" id="loginSubmitBtn" class="btn btn-dark w-100 py-2">Sign In</button>
                            </form>
                        </div>
                        
                        <div class="tab-pane fade" id="regTab" role="tabpanel" aria-labelledby="reg-tab-btn">
                            <div id="registerAlert" class="alert alert-danger d-none py-2 small"></div>
                            <form id="ajaxRegisterForm" method="post" action="<?= BASE_URL ?>/?r=auth/auth/register">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" name="fname" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" name="lname" class="form-control" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contact Number *</label>
                                    <input type="text" name="contact" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Home Address *</label>
                                    <input type="text" name="address" class="form-control" required>
                                </div>
                                <div class="row g-2 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Password *</label>
                                        <input type="password" name="password" class="form-control" minlength="8" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm *</label>
                                        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                                    </div>
                                </div>
                                <button type="submit" id="registerSubmitBtn" class="btn btn-dark w-100 py-2">Create Account</button>
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
</body>
</html>
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
  
    <script>
        window.BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>