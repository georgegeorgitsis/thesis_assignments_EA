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

    h3 {
        font-size: 16px;
    }
</style>

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
                        <h3>ΜΔΕ</h3>
                    </td>
                    <td>
                        <h3>Προτίμηση</h3>
                    </td>
                    <td>
                        <h3>Βαθμολογία κριτηρίων</h3>
                    </td>
                </tr>

                <?php
                $i = count($solution);

                for ($k = 0; $k < $i; $k++) {
                    $array_to_be_saved[$k]['thesis_id'] = $solution[$k]['thesis_id'];
                    $array_to_be_saved[$k]['student_id'] = $solution[$k]['student_id'];

                    ?>
                    <tr class="results-tr">
                        <td>
                            <?php echo $solution[$k]['student_id'] . " - " . $solution[$k]['student']['uacc_username']; ?>
                        </td>
                        <td>
                            <?php echo $solution[$k]['thesis_id'] . " - " . $solution[$k]['thesis']['title']; ?>
                        </td>
                        <td>
                            <?php echo $solution[$k]['priority']; ?>
                        </td>
                        <td>
                            <?php echo $solution[$k]['assessment']; ?>
                        </td>
                    </tr>
                    <?php
                }
                $json_en = json_encode($array_to_be_saved);
                ?>
            </table>

            <hr>

            <table id="population">
                <tr class="results-tr">
                    <td>
                        <h3>Αποδεκτά</h3>
                    </td>
                    <td>
                        <h3>Συγκρούσεις</h3>
                    </td>
                    <td>
                        <h3>1ες επιλογές</h3>
                    </td>
                    <td>
                        <h3>Γενιές</h3>
                    </td>
                    <td>
                        <h3>Ποιότητα</h3>
                    </td>
                    <td>
                        <h3>Χρ. εκτέλεσης</h3>
                    </td>
                </tr>
                <tr class="results-tr">
                    <td>
                        <?php echo $general_results['acceptable_genes']; ?>
                    </td>
                    <td>
                        <?php echo $general_results['collisions']; ?>
                    </td>
                    <td>
                        <?php echo $general_results['first_choises']; ?>
                    </td>
                    <td>
                        <?php echo $general_results['turns']; ?>
                    </td>
                    <td>
                        <?php echo $general_results['total_fitness']; ?>
                    </td>
                    <td>
                        <?php echo $general_results['execution_time']; ?>
                    </td>
                </tr>
            </table>
            <hr>
        </div>

        <script>
            $('#save_btn').click(function () {
                $.ajax({
                    url: "<?= base_url('assignment/save_to_database');?>",
                    dataType: "json",
                    method: "post",
                    data: { arr : <?= $json_en; ?>}

                }).done(function () {
                    $(this).addClass("done");
                });
            });
        </script>