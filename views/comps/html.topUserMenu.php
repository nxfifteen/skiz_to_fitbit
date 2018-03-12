<ul class="nav navbar-nav ml-auto">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <span class="d-md-down-none"><?php echo $resourceOwner['fullName'] ;?></span>
            <img src="<?php echo $resourceOwner['avatar150'] ;?>" class="img-avatar" alt="<?php echo $resourceOwner['displayName'] ;?>">
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">
                <strong>Account</strong>
            </div>

            <a class="dropdown-item" href="/logout.php"><i class="fa fa-lock"></i> Logout</a>
        </div>
    </li>
</ul>