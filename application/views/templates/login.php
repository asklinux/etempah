<div style="width: 320px; margin: 0 auto;">
   <h3>Login</h3>
   <?php if (!empty($error)): ?>
      <div class="alert alert-error">
         <b>Error!</b> <?= $error ?>
      </div>
   <?php elseif (!empty($info)): ?>
      <div class="alert alert-info">
         <b>Info.</b> <?= $info ?>
      </div>
   <?php endif; ?>
   <form class="well" method="POST">
      <label>Username</label>
      <input type="text" name="username" style="width: 260px;" <?php if (!empty($username)): ?> value="<?= $username ?>" <?php endif; ?>>
      <label>Password</label>
      <input type="password" name="password" style="width: 260px;">
      <button type="submit" class="btn btn-primary">Login</button>
   </form>
</div>
