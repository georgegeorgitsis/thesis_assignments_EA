<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">

<header class="navbar navbar-default navbar-static-top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?= base_url();?>" class="navbar-brand">DME Dpt Manager</a>
        </div>
        <nav class="collapse navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= base_url('dptmanager/student_management');?>">Manage Students</a>
                </li>
                <li>
                    <a href="<?= base_url('dptmanager/teacher_management');?>">Manage Teachers</a>
                </li>
                <li><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" href="#">Courses <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= base_url('dptmanager/assign_grades_to_students');?>">Grades to courses</a>
                        </li>
                        <li><a href="<?= base_url('dptmanager/course_management');?>">Courses</a></li>
                    </ul>

                </li>
                <li>
                    <a href="<?= base_url('dptmanager/department_settings');?>">Dpt Settings</a>
                </li>
                <li>
                    <a href="<?= base_url('assignment/get_data');?>">Thesis Assignment</a>
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
            <h2 id="sec0">Thesis Assignment Form</h2>
            <?php echo validation_errors(); ?>
            <?php echo form_open();?>
                <div class="form-group">
                    <label for="mo_sxolis">Μ.Ο. Σχολής</label>
                    <input type="range" name="mo_sxolis" id="mo_sxolis" value="50" min="0" max="100">
                </div>
                <div class="form-group">
                    <label for="mo_assigned_courses">Μ.Ο. Προαπαιτούμενων</label>
                    <input type="range" name="mo_assigned_courses" id="mo_assigned_courses" value="50" min="0" max="100">
                </div>
                <div class="form-group">
                    <label for="date_added">Ημερομηνία εισαγωγής</label>
                    <input type="range" name="date_added" id="date_added" value="50" min="0" max="100">
                </div>
                <div class="form-group">
                    <label for="bathmos_proodou">Βαθμός προόδου</label>
                    <input type="range" name="bathmos_proodou" id="bathmos_proodou" value="50" min="0" max="100">
                </div>
                <div class="form-group">
                    <label for="priority">Σειρά προτίμησης</label>
                    <input type="range" name="priority" id="priority" value="50" min="0" max="100">
                </div>
                <div class="form-group">
                    <label for="population_number">Αριθμός πληθυσμού</label>
                    <input type="text" name="population_number" id="population_number" >
                </div>

                <button type="submit" class="btn btn-default">Submit</button>
            <?php echo form_close();?>
            <hr>
        </div>
