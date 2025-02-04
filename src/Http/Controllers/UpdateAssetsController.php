<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Utils\Traits\CanNotify;
use Illuminate\Support\Facades\Artisan;

class UpdateAssetsController extends SharpProtectedController
{
    use CanNotify;
    
    public function __invoke()
    {
        try {
            Artisan::call('vendor:publish', [
                '--tag' => 'sharp-assets',
                '--force' => true
            ]);
        } catch (\Exception $e) {
            $this->notify('Error while updating assets')->setLevelDanger();
            return redirect()->back();
        }
        
        $this->notify('Assets updated successfully')->setLevelSuccess();
        return redirect()->back();
    }
}
