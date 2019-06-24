
<div id="cityInput" class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?=$base_url;?>">Application</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?=$base_url;?>/cities">Cities</a>
        </li>

        <?php if(empty($city)):?>

        <li class="breadcrumb-item active">Add a new city</li>

        <?php else:?>

        <li class="breadcrumb-item" id="breadcrumbCityName">
            <a href="<?=$base_url;?>/cities/<?=$city->getId();?>"><?=$city->getName();?></a>
        </li>
        <li class="breadcrumb-item active">Edit information</li>

        <?php endif;?>

    </ol>

    <!--
    <h1>City</h1>
    -->

    <!-- City -->

    <div id="cityFactory">

        <!-- Input data -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-city"></i>
                <?php if(empty($city)):?>Input city data
                <?php else:?><strong><?=$city->getName();?></strong> current information<?php endif;?>
            </div>
            <div class="card-body">

                <form id="cityForm">

                    <div class="form-group row">

                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>

                        <div class="col-sm-10">

                            <input id="inputName" type="text" class="form-control" placeholder="Boston" required 
                                <?php if(!empty($city)):?> value="<?=$city->getName();?>"<?php endif;?>>

                        </div>

                    </div>

                    <div class="form-group row">

                        <label for="inputLatitude" class="col-sm-2 col-form-label">Coordinates</label>

                        <div class="col-sm-10">

                            <input id="inputLatitude" type="number" class="form-control" min="-90" max="90" step="0.0000001" 
                                name="latitude" placeholder="42.3584328" required
                                <?php if(!empty($city)):?> value="<?=$city->getLatitude();?>"<?php endif;?>>

                        </div>

                    </div>             

                    <div class="form-group row">

                        <label for="inputLongitude" class="col-sm-2 col-form-label"></label>

                        <div class="col-sm-10">

                            <input id="inputLongitude" type="number" class="form-control" min="-180" max="180" step="0.0000001" 
                                name="longitude" placeholder="-71.0597763" required
                                <?php if(!empty($city)):?> value="<?=$city->getLongitude();?>"<?php endif;?>>

                        </div>

                    </div>  

                    <div class="form-group text-center">

                        <button id="citySubmitButton" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            <?php if(empty($city)):?>Submit<?php else:?>Save<?php endif;?>
                        </button>

                    </div> 

                </form>

            </div>

        </div>

        <!-- Result -->
        <div id="result" class="alert alert-success invisible"></div>

    </div>

</div><!-- /.container-fluid -->

