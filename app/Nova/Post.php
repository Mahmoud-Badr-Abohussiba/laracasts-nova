<?php

namespace App\Nova;

use App\Http\Middleware\TrimStrings;
use App\Nova\Actions\PublishPost;
use App\Nova\Filters\PostPublished;
use App\Nova\Lenses\MostTags;
use App\Nova\Metrics\PostCount;
use App\Nova\Metrics\PostsPerCategory;
use App\Nova\Metrics\PostsPerDay;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Post::class;

//    /**
//     * The single value that should be used to represent the resource when being displayed.
//     *
//     * @var string
//     */
//    public static $title = 'title';
//      * define a custom title

    public function title()
    {
        return $this->title.'-'.$this->category;
    }

    public function subtitle()
    {
       return 'Author:'. $this->user->name;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title', 'body'
    ];
// enable and disable global search
// public static $globallySearchable = false ;


    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id', $request->user()->id);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Title')->rules('required'),
            Trix::make('Body')->rules(['required']),
            DateTime::make('Publish Post At', 'publish_at')->hideFromIndex()
                ->rules('after_or_equal:today'),
            DateTime::make('Publish Post Until', 'publish_until')->hideFromIndex()
                ->rules('after_or_equal:publish_at'),
            Boolean::make('Is Published')->canSee(function ($request){
                return true; // specify who can see this field and who can't
            }),
            Select::make('category')->options([
                'tutorials' => 'Tutorials',
                'news' => 'News',
            ])->hideWhenUpdating()->rules('required'),

            BelongsTo::make('User')->rules('required'),
            BelongsToMany::make('Tags'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new PostsPerDay())->width('full'),
            (new PostCount())->width('1/2'),
            (new PostsPerCategory())->width('1/2'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new PostPublished(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new MostTags(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new PublishPost())->canSee(function ($request){
                return true;
            })->canRun(function($request, $post){
                return $post->id === 2;
            }),
        ];
    }

    // define your resource path
//    protected function resources()
//    {
//        Nova::resourcesIn(app_path('Nova'));
//
//        Nova::resources([
//            \App\Resource\User::class
//        ]);
//    }
    // define your resource path

}
