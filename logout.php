<?php
session_start();
session_destroy(); // Kill session
<a href="/logout.php" class="text-sm text-gray-600 hover:underline">Logout</a>
exit();
?>
