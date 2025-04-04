</main>

<footer>
    <hr>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> جميع الحقوق محفوظة - FlexAuto</p>
    </div>
</footer>

<?php
// تحديد مسار الجافاسكربت بناءً على مكان الصفحة (داخل admin أو لا)
$base_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>

<!-- ربط ملف الجافاسكربت -->
<script src="<?= $base_path; ?>assets/js/script.js"></script>

</body>
</html>
