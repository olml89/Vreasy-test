
<div id="citiesList" class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?=$base_url;?>">Application</a>
        </li>
        <li class="breadcrumb-item active">Cities</li>
    </ol>

    <!--
    <h1>Cities</h1>
    -->

    <!-- Cities -->

    <div id="citiesList">

        <?php 
            $icon = empty($search_criteria)? 'list' : 'search';
            $text = empty($search_criteria) ? 'Available cities' : 'Matching cities with the criteria:';
        ?>

        <div class="card mb-3">
            <div class="card-header">

                <i class="fas fa-<?=$icon;?>"></i>
                <?=$text;?>

                <?php if(!empty($search_criteria)): 
                foreach($search_criteria as $field => $value):?>
                    
                    <?=$field;?> = <strong><?=$value;?></strong>
                    <?php if($value !== end($search_criteria)):?>,<?php endif;?>

                <?php endforeach; endif;?>

            </div>
            <div class="card-body">

                <?php if(!empty($cities)):?>

                <table id="citiesTable" class="table table-bordered" width="100%" cellspacing="0">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach($cities as $city):?>

                        <tr class="cityRow" 
                            data-id="<?=$city->getId();?>" 
                            data-name="<?=$city->getName();?>"
                            data-latitude="<?=$city->getLatitude();?>"
                            data-longitude="<?=$city->getLongitude();?>"
                        >

                            <td class="cityViewLinkContainer">
                                <a href="<?=$base_url;?>/cities/<?=$city->getId();?>"><?=$city->getName();?></a>
                            </td>

                            <td><?=$city->getLatitude();?></td>
                            <td><?=$city->getLongitude();?></td>

                            <td class="text-center cityEditLinkContainer">
                                <a href="<?=$base_url;?>/cities/<?=$city->getId();?>/edit">
                                    <i class="fa fa-edit fa-2x"></i>
                                </a>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-primary cityDeleteButton">
                                    <i class="fa fa-trash-alt fa-2x"></i>
                                </button>
                            </td>

                        </tr>

                        <?php endforeach;?>

                    </tbody>

                </table>

                <?php endif;?>

            </div>
            <!--
            <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
            -->
        </div>

    </div>

    <?php if(!empty($pages) && count($pages) > 1):?>

    <div id="pagination" class="pagination">

        <p>Page:</p>

        <div class="pages-container">

            <?php foreach($pages as $index=>$is_current_page): if($index == 'left-separator' || $index == 'right-separator'):?>

                <span class="separator">...</span>

                <?php continue; endif;?>

                <?php if($is_current_page):?>

                <span class="current-page"><?=$index;?></span>

                <?php else:?>

                <a href="<?=$base_url.'/cities/page-'.$index;?>"><?=$index;?></a>

            <?php endif; endforeach;?>

        </div>

    </div>

    <?php endif;?>

</div><!-- /.container-fluid -->

