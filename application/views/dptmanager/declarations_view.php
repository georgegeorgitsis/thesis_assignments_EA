<style>
    .form-group {
        overflow: hidden;
    }
    .results-tr {
        width: 100%;
    }
    .results-tr td {
        border-bottom: 1px solid silver;
        margin: 10px 0px;
        width: 25%;
    }
    #population {
        width: 100%;
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

        <div class="col-md-12">
            <table id="population">
                <tr>
                </tr>

                <tr class="results-tr">
                    <td>
                        <h3>Φοιτητής</h3>
                    </td>
                    <td>
                        <h3>Πτυχιακή</h3>           
                    </td>
                    <td>
                        <h3>Προτίμηση</h3>
                    </td>
                </tr>

                <?php
                if (!empty($output)):

                    foreach ($output as $declaration) {
                        ?>
                        <tr class="results-tr">
                            <td>
                                <h5><?php echo $declaration['student_id'] . " - " . $declaration['student']; ?></h5>
                            </td>
                            <td>
                                <h5><?php echo $declaration['thesis_id'] . " - " . $declaration['thesis']; ?></h5>
                            </td>
                            <td>
                                <h5><?php echo $declaration['priority']; ?></h5>
                            </td>
                        </tr>
                        <?php
                    }

                endif;
                ?>
            </table>
        </div>
