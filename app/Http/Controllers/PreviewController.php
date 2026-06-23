<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Dev-only "design preview" controller.
 *
 * Lets a developer preview ANY signage design Vue component standalone in the
 * browser with realistic mock data. No database, screens, playlists or
 * websocket (Reverb/Echo) server required.
 *
 * This is gated to debug / local environments only.
 */
class PreviewController extends Controller
{
    public function __invoke(Request $request, $component = null)
    {
        // Dev-only tool: never expose in production.
        if (! config('app.debug') && ! app()->environment('local')) {
            abort(404);
        }

        $project = config('app.default_project') ?: (env('VITE_PROJECT_PATH') ?: 'Starter');

        $pages = $this->componentNames("resources/js/Projects/{$project}/Pages/*.vue");
        $layouts = $this->componentNames("resources/js/Projects/{$project}/Layouts/*.vue");
        $projects = $this->projectNames();

        $selectedPage = $component ?: ($pages[0] ?? null);

        $selectedLayout = $request->query('layout');
        if (! $selectedLayout) {
            $selectedLayout = in_array('None', $layouts, true) ? 'None' : ($layouts[0] ?? 'None');
        }

        return Inertia::render('Preview', [
            'project' => $project,
            'pages' => $pages,
            'layouts' => $layouts,
            'projects' => $projects,
            'selectedPage' => $selectedPage,
            'selectedLayout' => $selectedLayout,
        ]);
    }

    /**
     * Return the base names (without `.vue`) of files matching a glob,
     * relative to the application base path.
     */
    protected function componentNames(string $relativeGlob): array
    {
        $files = glob(base_path($relativeGlob)) ?: [];

        return collect($files)
            ->map(fn ($file) => pathinfo($file, PATHINFO_FILENAME))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Return the names of every project directory under resources/js/Projects.
     */
    protected function projectNames(): array
    {
        $dirs = glob(base_path('resources/js/Projects/*'), GLOB_ONLYDIR) ?: [];

        return collect($dirs)
            ->map(fn ($dir) => basename($dir))
            ->sort()
            ->values()
            ->all();
    }
}
