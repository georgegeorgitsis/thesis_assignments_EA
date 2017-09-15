<header class="navbar navbar-default navbar-static-top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">ΜΔΕ Student</a>
        </div>
        <nav class="collapse navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= base_url('student/display_thesis'); ?>">ΜΔΕ</a>
                </li>
                <li>
                    <a href="<?= base_url('student/thesis_declaration'); ?>">Δήλωση ΜΔΕ</a>
                </li>
                <li>
                    <a href="<?= base_url('login/logout'); ?>">Έξοδος</a>
                </li>

            </ul>
        </nav>
    </div>
</header>

<!-- Begin Body -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 id="sec0">Φοιτητής</h2>
            <div class="col-md-12">
                <div class="col-md-3">
                    <h3>Τίτλος ΜΔΕ</h3>
                </div>
                <div class="col-md-3">
                    <h3>Περιγραφή ΜΔΕ</h3>
                </div>
                <div class="col-md-3">
                    <h3>Καθηγητής</h3>
                </div>
                <div class="col-md-3">
                    <h3>Προαπαιτούμενα μαθήματα</h3>
                </div>
            </div>
            <?php
            foreach ($output as $thesis) {
                ?>
                <div class="col-md-12 each-thesis" style="border-top: 1px solid silver;padding: 10px 0px;">
                    <div class="col-md-3">
                        <?php echo $thesis['title']; ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $thesis['description']; ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $thesis['uacc_username']; ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                        foreach ($assigned_courses as $ac) {
                            if ($ac['thesis_id'] == $thesis['id']) {
                                echo $ac['course_code'] . '-' . $ac['name'] . "<br/>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <hr/>
                <?php
            }
            ?>

            <hr>
        </div>

        <style>
            .each-thesis:hover {
                background-color: silver;
            }

        </style>
