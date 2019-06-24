
<div id="cityOverview" class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?=$base_url;?>">Application</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?=$base_url.'/cities';?>">Cities</a>
        </li>
        <li class="breadcrumb-item active"><?=$city->getName();?></li>
    </ol>

    <!--
    <h1>City</h1>
    -->

    <!-- Status (if deleted, waiting to recover -->
    <div id="cityStatus" class="alert alert-danger hidden">This city is currently deleted</div>

    <!-- City -->

    <div id="cityView" 
        data-id="<?=$city->getId();?>" 
        data-name="<?=$city->getName();?>"
        data-latitude="<?=$city->getLatitude();?>"
        data-longitude="<?=$city->getLongitude();?>"
        >

        <!-- Coordinates -->
        <div id="cityCoordinates" class="card mb-3">

            <div class="card-header">
                <i class="fas fa-map-marked-alt"></i>
                <strong><?=$city->getName();?></strong> localization
            </div>

            <div class="card-body">

                <table id="coordinatesTable" class="table table-bordered" width="100%" cellspacing="0">

                    <tr id="latitude">
                        <th scope="row">Latitude</th>
                        <td><?=$city->getLatitude();?> º</td>
                    </tr>
                    
                    <tr id="longitude">
                        <th scope="row">Longitude</th>
                        <td><?=$city->getLongitude();?> º</td>
                    </tr>

                </table>

                <form>
                    <div class="form-group text-center">

                        <span id="cityEditLinkContainer">
                            <a class="btn btn-primary" href="<?=$base_url;?>/cities/<?=$city->getId();?>/edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </span>

                        <button type="button" class="btn btn-primary cityDeleteButton">
                            <i class="fa fa-trash-alt"></i> Delete
                        </button>

                    </div>
                </form>

            </div>

        </div>

        <!-- Sunrise-Sunset -->
        <?php 
            $sunriseSunset = $city->getSunriseSunset();
        ?>

        <?php if(!empty($sunriseSunset)):?>

        <div id="sunriseSunsetView" class="card mb-3">

            <div class="card-header">
                <i class="fas fa-sun"></i>
                Sunrise and Sunset
            </div>

            <div class="card-body">

                <?php if($sunriseSunset->isValid()):?>

                <table id="sunriseSunsetTable" class="table table-bordered" width="100%" cellspacing="0">

                    <tr id="sunrise">
                        <th scope="row">Sunrise</th>
                        <td><?=$sunriseSunset->getSunrise();?></td>
                    </tr>
                    
                    <tr id="sunset">
                        <th scope="row">Sunset</th>
                        <td><?=$sunriseSunset->getSunset();?></td>
                    </tr>

                </table>

                <?php else:?>

                <div class="alert alert-danger"><strong><?=$sunriseSunset->getTitle();?></strong>: <?=$sunriseSunset->getMessage();?></div>
                
                <?php endif;?>

                <form id="sunriseSunsetForm" method="POST" action="<?=$base_url;?>/cities/<?=$city->getId()?>">

                    Calculate again for a different date and time zone:

                    <div class="row form-group">

                        <div class="col">

                            <label for="timezoneSelector">Timezone</label>

                            <select class="form-control" id="timezoneSelector" name="timezone">

                                <?php 
                                foreach($timezones as $timezone):
                                    $selected = ($timezone === $datetimezone->getTimezone());

                                ?>

                                    <option value="<?=$timezone;?>"<?php if($selected):?> selected<?php endif;?>><?=$timezone;?></option>

                                <?php endforeach;?>
      
                            </select>

                        </div> 

                        <div class="col">
                            <label for="dateSelector">Date</label>
                            <input id="dateSelector" class="form-control" name="date" data-start-date="<?=$datetimezone->getDate();?>">
                        </div> 

                    </div>
                    

                    <div class="form-group text-center">
                        <button id="sunriseSunsetCalculateButton" type="submit" class="btn btn-primary">
                            <i class="fa fa-calculator"></i> Recalculate
                        </button>
                    </div>

                </form>

            </div>

            <div class="card-footer small text-muted">
                <span>On <?=$sunriseSunset->getDate();?>.</span>
                <span>API provided by <a href="https://sunrise-sunset.org/api">Sunrise Sunset</a>
            </div>

        </div>

        <?php endif;?>

    </div>

</div><!-- /.container-fluid -->

