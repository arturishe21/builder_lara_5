<?php

namespace Vis\Builder\Definitions;

use Vis\Builder\Services\Listing;
use Illuminate\Support\Arr;
use Vis\Builder\Fields\Definition;

class Resource
{
    protected $orderBy = 'created_at desc';
    protected $isSortable = false;
    protected $perPage = [20, 100, 1000];
    protected $cacheTag;
    protected $updateManyToManyList = [];
    protected $updateHasOneList = [];
    protected $relations = [];

    public function model()
    {
        return new $this->model;
    }

    public function getTitle()
    {
        return __cms($this->title);
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function getIsSortable()
    {
        return $this->isSortable;
    }

    public function getCacheKey()
    {
        return $this->cacheTag ?: $this->getNameDefinition();
    }

    public function clearCache()
    {
        Cache::tags($this->getCacheKey())->flush();
    }

    public function getOrderBy()
    {
        $sessionOrder = session($this->getSessionKeyOrder());

        if ($sessionOrder) {
            return $sessionOrder['field'] . ' ' . $sessionOrder['direction'];
        }

        return $this->orderBy;
    }


    public function getFilter()
    {
        return session($this->getSessionKeyFilter());;
    }

    public function getPerPageThis()
    {
        return session($this->getSessionKeyPerPage()) ? session($this->getSessionKeyPerPage())['per_page'] : $this->perPage[0];
    }

    public function getNameDefinition() : string
    {
        return mb_strtolower(class_basename($this));
    }

    public function getSessionKeyOrder() : string
    {
        return "table_builder.{$this->getNameDefinition()}.order";
    }

    public function getSessionKeyFilter() : string
    {
        return "table_builder.{$this->getNameDefinition()}.filter";
    }

    public function getSessionKeyPerPage() : string
    {
        return "table_builder.{$this->getNameDefinition()}.per_page";
    }

    public function getUrlAction() : string
    {
        $page = $this->getNameDefinition();

        return '/admin/actions/' . $page;
    }

    public function getAllFields() : array
    {
        $fields = $this->fields();
        $fields = isset($fields[0]) ? $fields : Arr::flatten($fields);

        $fieldsResults = [];
        foreach ($fields as $field) {
            $fieldsResults[$field->getNameField()] = $field;

            if ($field->getHasOne()) {
                $this->relations[] = $field->getHasOne();
            }
        }

        return $fieldsResults;
    }

    public function remove(int $id) : array
    {
        $this->model()->destroy($id);

        return [
            'status' => 'success'
        ];
    }

    public function clone(int $id) : array
    {
        $model = $this->model()->find($id);
        $newModel = $model->replicate();
        $newModel->push();

        return [
            'status' => 'success',
        ];
    }

    public function changeOrder($requestOrder, $params) : array
    {
        parse_str($requestOrder, $order);
        $pageThisCount = $params ?: 1;
        $perPage = 20;

        $lowest = ($pageThisCount * $perPage) - $perPage;

        foreach ($order['sort'] as $id) {
            $lowest++;

            $this->model()->where('id', $id)->update([
                'priority' => $lowest,
            ]);
        }

        return [
            'status' => 'success'
        ];
    }

    public function showAddForm()
    {
        $definition = $this;
        $fields = $this->fields();

        return [
            view('admin::new.form.create', compact('definition', 'fields'))->render()
        ];
    }

    public function showEditForm($id)
    {
        $definition = $this;

        $record = $this->model()->find($id);

        $fields = $this->fields();

        if (isset($fields[0])) {
            foreach ($fields as $field) {
                $field->setValue($record);
            }
        } else {
            foreach ($fields as $fieldBlock) {
                foreach ($fieldBlock as $field) {
                    $field->setValue($record);
                }
            }
        }

        return [
            'html' => view('admin::new.form.edit', compact('definition', 'fields'))->render(),
            'status' => true
        ];
    }

    public function saveAddForm($request)
    {
        $record = $this->model();
        $recordNew = $this->saveActive($record, $request);

        return [
            'id' => $recordNew->id,
            'html' => $this->getSingleRow($recordNew)
        ];
    }

    public function saveEditForm($request)
    {
        $recordNew = $this->updateForm($request);

        return [
            'id' => $recordNew->id,
            'html' => $this->getSingleRow($recordNew)
        ];
    }

    protected function updateForm($request)
    {
        $record = $this->model()->find($request['id']);
        $recordNew = $this->saveActive($record, $request);

        return $recordNew;
    }

    protected function saveActive($record, $request)
    {
        $fields = $this->getAllFields();

        foreach ($fields as $field) {
            $nameField = $field->getNameField();
            if ($nameField != 'id') {

                if ($field->getLanguage()) {
                    foreach ($field->getLanguage() as $langPrefix) {
                        $langField = $nameField . $langPrefix['postfix'];
                        $record->$langField = $request[$langField];
                    }
                }

                if ($field->getHasOne()) {
                    $this->updateHasOne($field, $request[$nameField]);
                    continue;
                }

                if ($field->isManyToMany()) {
                    $this->updateManyToMany($field, $request[$nameField] ?? '');
                    continue;
                }

                if ($field instanceof Definition) {
                    continue;
                }


                $record->$nameField = $request[$nameField];
            }
        }

        $record->save();

        if (count($this->updateManyToManyList)) {
            foreach ($this->updateManyToManyList as $item) {
                $item['field']->save($item['collectionsIds'], $record);
            }
        }

        if (count($this->updateHasOneList)) {
            foreach ($this->updateHasOneList as $item) {

                $relationHasOne = $item['field']->getHasOne();
                $data = [
                    $item['field']->getNameField() => $item['value']
                ];

                $record->$relationHasOne ? $record->$relationHasOne()->update($data) : $record->$relationHasOne()->create($data);
            }
        }

        return $record;
    }

    protected function updateManyToMany($field, $collectionsIds)
    {
        $this->updateManyToManyList[] = [
            'field' => $field,
            'collectionsIds' => $collectionsIds
        ];
    }

    protected function updateHasOne($field, $value)
    {
        $this->updateHasOneList[] = [
            'field' => $field,
            'value' => $value
        ];
    }

    protected function getSingleRow($recordNew)
    {
        $list = new Listing($this);
        $head = $list->head();
        $definition = $this;

        $recordNew->fields = clone $head;
        $head->map(function ($item2, $key) use ($recordNew, $definition) {
            $item2->setValue($recordNew);
            $recordNew->fields[$key]->value = $item2->getValueForList($definition);
        });

        return view('admin::new.list.single_row',
            [
                'list' => $list,
                'record' => $recordNew
            ]
        )->render();
    }

    public function getListing()
    {
        $head = $this->head();
        $list = $this->getCollection();
        $definition = $this;

        $list->map(function ($item, $key) use ($head, $definition) {
            $item->fields = clone $head;
            $item->fields->map(function ($item2, $key) use ($item, $definition) {
                $item->fields[$key] = clone $item2;
                $item2->setValue($item);

                $item->fields[$key]->value = $item2->getValueForList($definition);
            });
        });

        return $list;
    }

    protected function getCollection()
    {
        $collection = $this->model()->with($this->relations);
        $filter = $this->getFilter();
        $orderBy = $this->getOrderBy();
        $perPage = $this->getPerPageThis();

        if (isset($filter['filter']) && is_array($filter['filter'])) {
            foreach ($filter['filter'] as $field => $value) {
                if (is_null($value)) {
                    continue;
                }

                if (is_array($value)) {
                    if ($value['from'] && $value['to']) {
                        $collection = $collection->whereBetween($field, [$value['from'], $value['to']]);
                    }

                    continue;
                }

                $collection = $collection->where($field, '=', $value);
            }
        }

        return $collection->orderByRaw($orderBy)->paginate($perPage);
    }

    public function head()
    {
        $fields = $this->getAllFields();

        return collect($fields)->reject(function ($name) {
            return $name->isOnlyForm() == true;
        });
    }
}
