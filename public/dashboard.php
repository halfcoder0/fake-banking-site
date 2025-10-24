<?php $name='test'; ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | NexaBank</title>
    <link rel="stylesheet" href="./css/style.css">
  </head>
<body>
  <div class="wrapper">
    <h2>Welcome !! <?php echo htmlspecialchars($name); ?>
      <div class="input-box button">
    <a href="/logout">Logout</a>
  </div>
</h2>
  </div>
</body>
</html>
