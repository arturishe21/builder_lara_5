<?php

namespace Vis\Builder\Models;

use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;
use Bkwld\Cloner\Cloneable;
use Vis\Builder\Http\Traits\{Rememberable, TranslateTrait, SeoTrait, ImagesTrait, ViewPageTrait, QuickEditTrait};

class Tree extends Model
{
    use Rememberable,
        TranslateTrait,
        SeoTrait,
        ImagesTrait,
        ViewPageTrait,
        RevisionableTrait,
        QuickEditTrait,
        Cloneable,
        NodeTrait;

    public function getLftName(): string
    {
        return 'lft';
    }

    public function getRgtName(): string
    {
        return 'rgt';
    }

    public function getParentIdName(): string
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
        return $this
            ->hasMany(get_class($this), $this->getParentIdName())
            ->setModel($this)
            ->defaultOrder();
    }

    protected $fillable = [];

    protected array $revisionFormattedFieldNames = [
        'title'             => 'Название',
        'description'       => 'Описание',
        'is_active'         => 'Активация',
        'picture'           => 'Изображение',
        'short_description' => 'Короткий текст',
        'created_at'        => 'Дата создания',
    ];

    protected array $revisionFormattedFields = [
        '1'          => 'string:<strong>%s</strong>',
        'public'     => 'boolean:No|Yes',
        'deleted_at' => 'isEmpty:Active|Deleted',
    ];

    protected bool $revisionEnabled = true;
    protected bool $revisionCleanup = true;
    protected int $historyLimit = 500;

    protected string $fileDefinition = 'tree';

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function setFillable(array $params): void
    {
        $this->fillable = $params;
    }

    protected $table = 'tb_tree';

    protected $_nodeUrl;

    public function checkUnicUrl(): void
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

    public function setUrl(string $url): void
    {
        $this->_nodeUrl = $url;
    }

    public function getUrl(): string
    {
        if (! $this->_nodeUrl) {
            $this->_nodeUrl = $this->getGeneratedUrl();
        }

        if (strpos($this->_nodeUrl, 'http') !== false) {
            return $this->_nodeUrl;
        }

        return '/'. $this->_nodeUrl;
    }

    public function getUrlNoLocation(): string
    {
        if (! $this->_nodeUrl) {
            $this->_nodeUrl = $this->getGeneratedUrl();
        }

        return '/'. $this->_nodeUrl;
    }

    public function getGeneratedUrl(): string
    {
        $tags = $this->getCacheTags();

        if ($tags && $this->fileDefinition) {
            return Cache::tags($tags)->rememberForever($this->fileDefinition.'_'.$this->id, function () {
                return $this->getGeneratedUrlInCache();
            });
        }

        return $this->getGeneratedUrlInCache();
    }

    public function getAncestorsAndSelf()
    {
        return self::defaultOrder()->ancestorsAndSelf($this->id);
    }

    private function getGeneratedUrlInCache(): string
    {
        $all = $this->getAncestorsAndSelf();
        $slugs = [];

        foreach ($all as $node) {
            if ($node->slug === '/') {
                continue;
            }

            $slugs[] = $node->slug;
        }

        return implode('/', $slugs);
    }

    public function isHasChildren(): bool
    {
        return (bool) $this->children_count;
    }

    public function clearCache(): void
    {
        $tags = $this->getCacheTags();

        if (count($tags)) {
            Cache::tags($tags)->flush();
        }
    }

    protected function getCacheTags(): array
    {
        return ['tree'];
    }
}
