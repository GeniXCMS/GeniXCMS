            </div> <!-- .install-body -->
            <div class="install-footer">
                <div class="small text-muted">
                    &copy; 2014-<?=date('Y');?> <span class="fw-bold">GeniXCMS</span>. All rights reserved.
                </div>
                <div class="small">
                    <?php
                        $end_time = microtime(TRUE);
                        $time_taken = $end_time - $GLOBALS['start_time'];
                        $time_taken = round($time_taken,5);
                        echo '<small class="text-muted opacity-50 me-3">Rendered in '.$time_taken.'s</small>';
                    ?>
                    <a href="https://genixcms.id" target="_blank" class="text-decoration-none text-muted">Documentation</a>
                </div>
            </div>
        </div> <!-- .install-card -->
    </div> <!-- .install-container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
