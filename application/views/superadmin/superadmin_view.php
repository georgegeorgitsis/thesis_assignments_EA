<header class="navbar navbar-default navbar-static-top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">ΜΔΕ Superadmin</a>
        </div>
        <nav class="collapse navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= base_url('superadmin/dpt_management');?>">Τμήματα</a>
                </li>
                <li>
                    <a href="<?= base_url('superadmin/dpt_keys');?>">API keys</a>
                </li>
                <li>
                    <a href="<?= base_url('superadmin/dpt_administrator_management');?>">Χρήστες</a>
                </li>
                <li>
                    <a href="<?= base_url('superadmin/settings');?>">Ρυθμίσεις τμήματος</a>
                </li>
                <li>
                    <a href="<?= base_url('login/logout');?>">Έξοδος</a>
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
            <h2 id="sec0">Superadmin</h2>

            <?php if (!empty($output)): ?>
                <?php echo $output->output; ?>
            <?php endif; ?>


            <hr>
        </div>