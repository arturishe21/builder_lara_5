<?php

namespace Vis\Builder;

use Illuminate\Support\Facades\Cache;
use Request;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tree.
 */
class Tree extends Model
{
    use \Vis\Builder\Helpers\Traits\Rememberable,
        \Vis\Builder\Helpers\Traits\TranslateTrait,
        \Vis\Builder\Helpers\Traits\SeoTrait,
        \Vis\Builder\Helpers\Traits\ImagesTrait,
        \Vis\Builder\Helpers\Traits\ViewPageTrait,
        \Venturecraft\Revisionable\RevisionableTrait,
        \Vis\Builder\Helpers\Traits\QuickEditTrait,
        \Bkwld\Cloner\Cloneable,
        NodeTrait;

    public function getLftName()
    {
        return 'lft';
    }

    public function getRgtName()
    {
        return 'rgt';
    }

    public function getParentIdName()
    {
        return 'parent_id';
    }

    public function makeFirstChildOf($root)
    {
        $this->appendToNode($root)->save();
    }

    public function makeChildOf($root)
    {
        $this->appendToNode($root)->save();
    }

    public function children()
    {
        return $this->hasMany(get_class($this), $this->getParentIdName())
            ->setModel($this)->defaultOrder();
    }

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

    protected $table = 'tb_tree';

    protected $_nodeUrl;

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

    public function getAncestorsAndSelf()
    {
        return self::defaultOrder()->ancestorsAndSelf($this->id);
    }

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
    protected function getCacheTags()
    {
        return ['tree'];
    }
}
