</main>

<footer class="py-2 text-center text-body-secondary bg-body-tertiary">
  <div class="container">
    <?php
    echo Menus::getMenu('footer', 'nav mx-auto mb-2 mb-lg-0', true);
    ?>
  </div>
  <p>Blog template built for <a href="https://getbootstrap.com/">Bootstrap</a> by <a
      href="https://twitter.com/mdo">@mdo</a>.</p>
  <p class="mb-0">
    <a href="#">Back to top</a>
  </p>
</footer>

<?php
Site::footer();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"
  integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QVYTCJNMPC"></script>
</body>

</html>