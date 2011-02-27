This extension uses the infinite scroll jQuery plugin, from http://www.infinite-scroll.com/ to create an infinite scrolling pagination, like in twitter. This kind of pagination is also called Endless Scroll.

It uses javascript to load and parse the new pages, but gracefully degrade in cases where javascript is disabled and the users will still be able to access all the pages.

##Requirements

Yii 1.1.4

##Usage

The YiinfiniteScroller class extends the CBasePager class, so you will use it the same way as CLinkPager and CListPager.

On your controller you need to create the CPagination class which controls the pages handling:

class PostController extends Controller
{
	public function actionIndex()
	{
            $criteria = new CDbCriteria;
            $total = Post::model()->count();

            $pages = new CPagination($total);
            $pages->pageSize = 50;
            $pages->applyLimit($criteria);

            $posts = Post::model()->findAll($criteria);

	    $this->render('index', array(
                'posts' => $posts,
                'pages' => $pages,
            ));
	}
}


Now on your view you will use it as a widget, like in the following sample:

$this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
    'itemSelector' => 'div.post',
    'pages' => $pages,
));


There are a few properties that can be set for YiinfiniteScroller:

- contentSelector: The jQuery selector of the container element where the loaded items (from itemSelector) will be appended
- loadingText: The text to be displayed and a new page is being loaded
- donetext: The text to be displayed when all the pages had been loaded
- pages:  The CPagination object
- errorHandler: Javascrit function that is called when the ajax errors or 404s.
