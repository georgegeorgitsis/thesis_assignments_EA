<header class="navbar navbar-default navbar-static-top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">ΜΔΕ Dpt Manager</a>
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

        <div class="col-md-12">
            <h2 id="sec0">Dpt Manager</h2>

            <?php if (!empty($output)): ?>
                <?php echo $output->output; ?>
            <?php endif; ?>

            <hr>
        </div>