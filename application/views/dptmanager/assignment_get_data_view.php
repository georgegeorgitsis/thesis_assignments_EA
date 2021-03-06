<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">

<style>
    .form-group {
        overflow: hidden;
    }    
    .results-tr td {
        border-bottom: 1px solid silver;
    }
</style>
<header class="navbar navbar-default navbar-static-top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">Διαχειριστής</a>
        </div>
        <nav class="collapse navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= base_url('dptmanager/student_management'); ?>">Φοιτητές</a>
                </li>
                <li>
                    <a href="<?= base_url('dptmanager/teacher_management'); ?>">Καθηγητές</a>
                </li>
                <li><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" href="#">Μαθήματα <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= base_url('dptmanager/course_management'); ?>">Μαθήματα</a></li>
                        <li>
                            <a href="<?= base_url('dptmanager/assign_grades_to_students'); ?>">Βαθμοί</a>
                        </li>
                    </ul>

                </li>
                <li>
                    <a href="<?= base_url('dptmanager/department_settings'); ?>">Ρυθμίσεις</a>
                </li>
                <li>
                    <a href="<?= base_url('dptmanager/show_declarations'); ?>">Δηλώσεις</a>
                </li>
                <li>
                    <a href="<?= base_url('assignment/get_data'); ?>">Αναθέσεις</a>
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
        <?php if ($this->session->flashdata('message')): ?>
            <div class='row'>
                <div class="col-md-12">
                    <div class="alert alert-dismissable alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong></strong> <?= $this->session->flashdata('message'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class='row'>
                <div class="col-md-12">
                    <div class="alert alert-dismissable alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong></strong> <?= $this->session->flashdata('error'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-md-12 ui-content" data-role="main">
            <h2 id="sec0">Ανάθεση ΜΔΕ</h2>
            <?php echo validation_errors(); ?>
            <?php echo form_open(); ?>
            <div class="form-group">
                <div class="col-lg-6">
                    <label for="mo_sxolis">Μ.Ο. Σχολής</label>
                </div>
                <div class="col-lg-6">
                    0<input type="range" name="mo_sxolis" id="mo_sxolis" value="100" min="1" max="100">100
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6">
                    <label for="mo_assigned_courses">Μ.Ο. Προαπαιτούμενων</label>
                </div>
                <div class="col-lg-6">
                    0<input type="range" name="mo_assigned_courses" id="mo_assigned_courses" value="100" min="1" max="100">100
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6">
                    <label for="date_added">Ημερομηνία εισαγωγής</label>
                </div>
                <div class="col-lg-6">
                    0<input type="range" name="date_added" id="date_added" value="100" min="1" max="100">100
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6">
                    <label for="bathmos_proodou">Βαθμός προόδου</label>
                </div>
                <div class="col-lg-6">
                    0<input type="range" name="bathmos_proodou" id="bathmos_proodou" value="100" min="1" max="100">100
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6">
                    <label for="priority">Σειρά προτίμησης</label>
                </div>
                <div class="col-lg-6">
                    0<input type="range" name="priority" id="priority" value="100" min="1" max="100">100
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6">
                    <label for="population_number">Αριθμός πληθυσμού</label>
                </div>
                <div class="col-lg-6">
                    <input type="text" name="population_number" id="population_number" required="required" value="100">
                </div>
            </div>

            <button type="submit" class="btn btn-default">Submit</button>
            <?php echo form_close(); ?>
            <hr>
        </div>
