<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Menu\SharpMenuUserMenu;
use Illuminate\View\Component;

class UserDropdown extends Component
{
    use HasSharpMenu;
    public string $username;

    public function __construct()
    {
        $this->username = sharp_user()->{config('sharp.auth.display_attribute', 'name')};
    }

    public function getUserMenu(): ?SharpMenuUserMenu
    {
        return $this
            ->getSharpMenu()
            ?->userMenu();
    }

    public function render()
    {
        return view('sharp::components.user-dropdown', [
            'self' => $this,
        ]);
    }
}
