<?php
$title = "Agam - Staff Login";
$bodyClass = "login-page";

$role = $_GET['role'] ?? 'kitchen';
$roleName = ($role === 'kitchen') ? 'Kitchen Staff' : (($role === 'waiter') ? 'Waiter Staff' : 'Restaurant Owner');
$placeholder = ($role === 'owner') ? 'Enter owner password...' : 'Enter staff PIN...';

ob_start();
?>

<div class="login-wrapper">
    <div class="card login-card">
        <div class="login-header">
            <div class="login-brand-icon">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <h2>Agam<span style="color:var(--emenu-gold);font-weight:400;">eMenu</span></h2>
            <h3 style="font-size:1rem;font-weight:500;color:#94a3b8;margin-top:4px;"><?= htmlspecialchars($roleName) ?> Access</h3>
            <p>Please enter your access code to proceed</p>
        </div>

        <div class="role-selector-pills">
            <a href="/login?role=kitchen" class="role-pill <?= $role === 'kitchen' ? 'active' : '' ?>"><i class="fa-solid fa-fire-burner"></i> Kitchen</a>
            <a href="/login?role=waiter" class="role-pill <?= $role === 'waiter' ? 'active' : '' ?>"><i class="fa-solid fa-bell-concierge"></i> Waiter</a>
            <a href="/login?role=owner" class="role-pill <?= $role === 'owner' ? 'active' : '' ?>"><i class="fa-solid fa-user-shield"></i> Owner</a>
        </div>

        <form id="login-form" class="login-form">
            <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
            
            <div class="form-group">
                <label for="pin-input">PIN / Password</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" id="pin-input" name="pin" placeholder="<?= htmlspecialchars($placeholder) ?>" required autofocus class="form-control">
                </div>
            </div>

            <div id="login-error" class="alert alert-danger hidden"></div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Access Portal <i class="fa-solid fa-right-to-bracket"></i>
            </button>

            <div class="login-hint">
                <small><i class="fa-solid fa-circle-info"></i> Demo PINs: Kitchen/Waiter = <code>1234</code> | Owner = <code>admin123</code></small>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
