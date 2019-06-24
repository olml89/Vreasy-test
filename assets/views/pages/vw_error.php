<div id="errorView" class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?=base_url();?>">Application</a>
        </li>
        <li class="breadcrumb-item active">Error <?=$exception->getCode();?></li>
    </ol>

    <!-- Page Content -->
    <h1><?=$exception->getTitle();?></h1>
    <hr>
    <p><?=$exception->getMessage();?></p>

</div><!-- /.container-fluid -->