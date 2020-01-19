<?php

namespace Vis\Builder;

use Illuminate\Support\Facades\Cache;
use Request;

/**
 * Class Tree.
 */
class Tree extends \Baum\Node
{
    use \Vis\Builder\Helpers\Traits\Rememberable,
        \Vis\Builder\Helpers\Traits\TranslateTrait,
        \Vis\Builder\Helpers\Traits\SeoTrait,
        \Vis\Builder\Helpers\Traits\ImagesTrait,
        \Vis\Builder\Helpers\Traits\ViewPageTrait,
        \Venturecraft\Revisionable\RevisionableTrait;

    /**
     * @var array
     */
    protected $fillable = [];
    /**
     * @var array
     */
    protected $revisionFormattedFieldNames = [
        'title'             => 'Название',
        'description'       => 'Описание',
        'is_active'         => 'Активация',
        'picture'           => 'Изображение',
        'short_description' => 'Короткий текст',
        'created_at'        => 'Дата создания',
    ];
    /**
     * @var array
     */
    protected $revisionFormattedFields = [
        '1'          => 'string:<strong>%s</strong>',
        'public'     => 'boolean:No|Yes',
        'deleted_at' => 'isEmpty:Active|Deleted',
    ];
    /**
     * @var bool
     */
    protected $revisionEnabled = true;
    /**
     * @var bool
     */
    protected $revisionCleanup = true;
    /**
     * @var int
     */
    protected $historyLimit = 500;

    /**
     * @var string
     */
    protected $fileDefinition = 'tree';

    /**
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * @param array $params
     */
    public function setFillable(array $params)
    {
        $this->fillable = $params;
    }

    public static function boot()
    {
        parent::boot();
    }

    /**
     * @var string
     */
    protected $table = 'tb_tree';
    /**
     * @var string
     */
    protected $parentColumn = 'parent_id';

    // 'lft' column name
    protected $leftColumnName = 'lft';

    // 'rgt' column name
    protected $rightColumnName = 'rgt';

    // 'depth' column name
    protected $depthColumnName = 'depth';

    /**
     * @var
     */
    protected $_nodeUrl;
    /**
     * @var
     */
    private $treeMy;
    /**
     * @var
     */
    private $treeOptions;
    /**
     * @var
     */
    private $recursiveOnlyLastLevel;

    // end setSlugAttribute

    public function checkUnicUrl()
    {
        $slug = $this->slug;
        if ($slug) {
            $slugCheck = $this->where('slug', 'like', $this->slug)
                ->where('parent_id', $this->parent_id)
                ->where('id', '!=', $this->id)->count();

            if ($slugCheck) {
                $slug = $this->slug.'_'.$this->id;
            }

            $slugCheckId = $this->where('slug', 'like', $slug)
                ->where('parent_id', $this->parent_id)
                ->where('id', '!=', $this->id)->count();

            if ($slugCheckId) {
                $slug = $slug.'_'.time();
            }

            $this->slug = $slug;
            $this->save();
        }
    }

    /**
     * @return bool
     */
    public function hasTableDefinition()
    {
        $templates = config('builder.tree.templates');
        $template = config('builder.tree.default');
        if (isset($templates[$this->template])) {
            $template = $templates[$this->template];
        }

        return $template['type'] == 'table';
    }

    // end hasTableDefinition

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->_nodeUrl = $url;
    }

    // end setUrl

    /**
     * @return mixed|string
     */
    public function getUrl()
    {
        if (! $this->_nodeUrl) {
            $this->_nodeUrl = $this->getGeneratedUrl();
        }

        if (strpos($this->_nodeUrl, 'http') !== false) {
            return $this->_nodeUrl;
        }

        return '/'.$this->_nodeUrl;
    }

    /**
     * @return string
     */
    public function getUrlNoLocation()
    {
        if (! $this->_nodeUrl) {
            $this->_nodeUrl = $this->getGeneratedUrl();
        }

        return '/'.$this->_nodeUrl;
    }

    /**
     * @return mixed|string
     */
    public function getGeneratedUrl()
    {
        $tags = $this->getCacheTags();

        if ($tags && $this->fileDefinition) {
            return Cache::tags($tags)->rememberForever($this->fileDefinition.'_'.$this->id, function () {
                return $this->getGeneratedUrlInCache();
            });
        }

        return $this->getGeneratedUrlInCache();
    }

    // end getGeneratedUrl

    /**
     * @return string
     */
    private function getGeneratedUrlInCache()
    {
        $all = $this->getAncestorsAndSelf();
        $slugs = [];

        foreach ($all as $node) {
            if ($node->slug == '/') {
                continue;
            }

            $slugs[] = $node->slug;
        }

        return implode('/', $slugs);
    }

    public function isHasChildren()
    {
        return (bool) $this->children_count;
    }

    public function clearCache()
    {
        $tags = $this->getCacheTags();

        if (count($tags)) {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * @return bool|mixed
     */
    private function getCacheTags()
    {
        return ['tree'];
    }

    /**
     * @param $id
     * @param bool $recursiveOnlyLastLevel
     *
     * @return mixed
     */
    public function getCategory($id, $recursiveOnlyLastLevel = false)
    {
        $this->recursiveOnlyLastLevel = $recursiveOnlyLastLevel;
        $node = \Tree::find($id);
        $children = $node->descendants()->get(['id', 'title', 'parent_id'])->toArray();
        $result = [];

        foreach ($children as $row) {
            $result[$row['parent_id']][] = $row;
        }

        $this->treeMy = $result;
        $this->printCategories($id, 0);

        return $this->treeOptions;
    }

    /**
     * @param $parent_id
     * @param $level
     */
    private function printCategories($parent_id, $level)
    {
        if (isset($this->treeMy[$parent_id])) {
            foreach ($this->treeMy[$parent_id] as $value) {
                $disable = isset($this->treeMy[$value['id']]) && $this->recursiveOnlyLastLevel ? 'disabled' : '';

                $paddingLeft = '';

                for ($i = 0; $i < $level; $i++) {
                    $paddingLeft .= '--';
                }

                $this->treeOptions[$value['id']] = "<option $disable value ='".$value['id']."'>".$paddingLeft.$value['title'].'</option>';
                $level = $level + 1;
                $this->printCategories($value['id'], $level);
                $level = $level - 1;
            }
        }
    }
}
