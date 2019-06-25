<div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">

        <?php if(!empty($sidebar)): foreach($sidebar as $link):?>

        <li class="nav-item<?php if($link['active']):?> active<?php endif;?>">
            <a class="nav-link" href="<?=$base_url.$link['uri'];?>">
                <i class="fas fa-fw fa-<?=$link['icon'];?>"></i>
                <span><?=$link['text'];?></span>
            </a>
        </li>

        <?php endforeach; endif;?>

    </ul>

    <div id="content-wrapper">