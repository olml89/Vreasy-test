<nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="<?=$base_url;?>">vreasy Test</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Search box -->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0" method="POST" action="<?=$base_url;?>/cities/search">
        <div class="input-group">

            <?php
                $name       = $search_criteria['name'] ?? '';
                $latitude   = $search_criteria['latitude'] ?? '';
                $longitude  = $search_criteria['longitude'] ?? '';
            ?>

            <input type="text" class="form-control" placeholder="Boston" aria-label="Search" aria-describedby="basic-addon2" 
                name="name"<?php if(!empty($name)):?> value="<?=$name;?>"<?php endif;?>>

            <input type="text" class="form-control" placeholder="42.358433" aria-label="Search" aria-describedby="basic-addon2" 
                name="latitude"<?php if(!empty($latitude)):?> value="<?=$latitude;?>"<?php endif;?>>

            <input type="text" class="form-control" placeholder="-71.059776" aria-label="Search" aria-describedby="basic-addon2" 
                name="longitude"<?php if(!empty($longitude)):?> value="<?=$longitude;?>"<?php endif;?>>

            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>

        </div>
    </form>

</nav>