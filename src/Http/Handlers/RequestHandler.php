<?php

namespace Vis\Builder\Handlers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Vis\Builder\JarboeController;
use Vis\Builder\Revision;

/**
 * Class RequestHandler.
 */
class RequestHandler
{
    /**
     * @var JarboeController
     */
    protected $controller;
    /**
     * @var mixed
     */
    protected $definitionName;
    /**
     * @var mixed
     */
    protected $definition;

    /**
     * RequestHandler constructor.
     *
     * @param JarboeController $controller
     */
    public function __construct(JarboeController $controller)
    {
        $this->controller = $controller;
        $this->definitionName = $controller->getOption('def_name');
        $this->definition = $controller->getDefinition();
    }

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function handle()
    {
        switch (request('query_type')) {
            case 'search':
                return $this->handleSearchAction();

            case 'change_order':
                return $this->handleChangeOrderAction();

            case 'multi_action':
                return $this->handleMultiAction();

            case 'multi_action_with_option':
                return $this->handleMultiActionWithOption();

            case 'import':
                return $this->handleImport();

            case 'get_import_template':
                return $this->handleImportTemplateDownload();

            case 'export':
                return $this->handleExport();

            case 'set_per_page':
                return $this->handleSetPerPageAmountAction();

            case 'show_edit_form':
                return $this->handleShowEditFormAction();

            case 'show_revisions':
                return $this->handleShowRevisionForm();

            case 'show_views_statistic':
                return $this->handleShowViewsStatisic();

            case 'return_revisions':
                return $this->handleReturnRevisions();

            case 'save_edit_form':
                return $this->handleSaveEditFormAction();

            case 'show_add_form':
                return $this->handleShowAddFormAction();

            case 'save_add_form':
                return $this->handleSaveAddFormAction();

            case 'delete_row':
                return $this->handleDeleteAction();

            case 'fast_save':
                return $this->handleFastSaveAction();

            case 'clone_record':
                return $this->handleCloneAction();

            case 'upload_photo':
                return $this->handlePhotoUpload();

            case 'change_direction':
                return $this->handleChangeDirection();

            case 'upload_file':
                return $this->handleFileUpload();

            case 'many_to_many_ajax_search':
                return $this->handleForeignAjaxSearch();

            case 'select_with_uploaded':
                return $this->handleSelectWithUploaded();

            case 'select_with_uploaded_images':
                return $this->handleSelectWithUploadedImages();

            case 'get_html_foreign_definition':
                return $this->handleShowHtmlForeignDefinition();

            case 'delete_foreign_row':
                return $this->handleDeleteForeignDefinition();

            case 'change_position':
                return $this->handleChangePositionDefinition();

            case 'foreign_ajax_search':
                return $this->handleForeignAjaxSearch();

            case 'clear_order_by':
                return $this->handleClearOrderBy();

            default:
                return $this->handleShowList();
        }
    }

    /**
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleSelectWithUploaded()
    {
        $result = $this->controller->query->getUploadedFiles();

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleSelectWithUploadedImages()
    {
        $field = $this->controller->getField(request('baseName'));

        $result = $this->controller->query->getUploadedImages($field);

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleForeignAjaxSearch()
    {
        $field = $this->controller->getField(request('ident'));

        $data = $field->getAjaxSearchResult(request('q'), request('limit'), request('page'));

        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleChangeOrderAction()
    {
        $this->controller->query->clearCache();
        parse_str(request('order'), $order);

        if (count($this->controller->getFiltersDefinition())
          || count($this->controller->getOrderDefinition())
        ) {
            return Response::json([
                'status'  => false,
                'message' => 'Изменение порядка невозможно при фильтрации и сортировки',
            ]);
        }

        $pageThisCount = request('params') ?: 1;

        $perPage = $this->controller->query->getPerPageAmount($this->definition['db']['pagination']['per_page']);

        $lowest = ($pageThisCount * $perPage) - $perPage;

        foreach ($order['sort'] as $id) {
            $lowest++;
            \DB::table($this->definition['db']['table'])->where('id', $id)->update([
                'priority' => $lowest,
            ]);
        }

        return Response::json(['status' => true]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|void
     */
    protected function handleMultiAction()
    {
        $type = request('type');
        $ids = request('multi_ids');

        if (! $ids) {
            return;
        }

        $action = $this->definition['multi_actions'][$type];

        $isAllowed = $action['check'];

        if (! $isAllowed()) {
            throw new \RuntimeException('Multi action not allowed: '.$type);
        }

        $handlerClosure = $action['handle'];

        $arrayIds = explode(',', $ids);

        $data = $handlerClosure($arrayIds);

        $data['ids'] = $arrayIds;
        $data['is_hide_rows'] = false;

        if (isset($action['is_hide_rows'])) {
            $data['is_hide_rows'] = $action['is_hide_rows'];
        }

        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleMultiActionWithOption()
    {
        $type = request('type');
        $option = request('option');
        $action = $this->definition['multi_actions'][$type];

        $isAllowed = $action['check'];
        if (! $isAllowed()) {
            throw new \RuntimeException('Multi action not allowed: '.$type);
        }

        $ids = request('multi_ids', []);
        $handlerClosure = $action['handle'];
        $data = $handlerClosure($ids, $option);

        $data['ids'] = $ids;
        $data['is_hide_rows'] = false;

        if (isset($action['is_hide_rows'])) {
            $data['is_hide_rows'] = $action['is_hide_rows'];
        }

        return Response::json($data);
    }

    protected function handleImportTemplateDownload()
    {
        $type = request('type');
        $method = 'do'.ucfirst($type).'TemplateDownload';

        $this->controller->import->$method();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleImport()
    {
        $file = Input::file('file');
        $type = request('type');
        $method = 'doImport'.ucfirst($type);

        return Response::json([
            'status' => $this->controller->import->$method($file),
        ]);
    }

    protected function handleExport()
    {
        $this->controller->export->doExport(request('type'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleSetPerPageAmountAction()
    {
        $perPage = request('per_page');

        $sessionPath = 'table_builder.'.$this->definitionName.'.per_page';
        Session::put($sessionPath, $perPage);

        return Response::json([
            'url' => $this->controller->getOption('url'),
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleChangeDirection()
    {
        $order = [
            'direction' => request('direction'),
            'field'     => request('field'),
        ];

        $sessionPath = 'table_builder.'.$this->definitionName.'.order';
        Session::put($sessionPath, $order);

        return Response::json([
            'url' => $this->controller->getOption('url'),
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    protected function handleFileUpload()
    {
        $file = Input::file('file');
        $prefixPath = trim(request('path', '/storage/files'), '/');
        $prefixPath .= '/';

        if ($this->controller->hasCustomHandlerMethod('onFileUpload')) {
            $res = $this->controller->getCustomHandler()->onFileUpload($file);
            if ($res) {
                return $res;
            }
        }

        $extension = $file->getClientOriginalExtension();
        $nameFile = explode('.', $file->getClientOriginalName());
        $fileName = \Jarboe::urlify($nameFile[0]).'.'.$extension;

        if (file_exists(public_path().'/'.$prefixPath.$fileName)) {
            $fileName = \Jarboe::urlify($nameFile[0]).'_'.time().'.'.$extension;
        }

        $destinationPath = $prefixPath;

        $file->move($destinationPath, $fileName);

        $data = [
            'status'     => true,
            'link'       => URL::to($destinationPath.$fileName),
            'short_link' => $fileName,
            'long_link'  => '/'.$destinationPath.$fileName,
        ];

        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    protected function handlePhotoUpload()
    {
        $this->controller->query->clearCache();

        $baseIdent = request('baseIdent');
        $file = Input::file('image');

        $field = $this->controller->getField($baseIdent);

        if ($this->controller->hasCustomHandlerMethod('onPhotoUpload')) {
            $res = $this->controller->getCustomHandler()->onPhotoUpload($field, $file);
            if ($res) {
                return $res;
            }
        }

        $data = $field->doUpload($file);

        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleDeleteAction()
    {
        $idRow = $this->getRowID();
        $this->checkEditPermission($idRow);

        $result = $this->controller->query->deleteRow($idRow);

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleFastSaveAction()
    {
        $result = $this->controller->query->fastSave(Input::all());
        $result['status'] = 'ok';

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleCloneAction()
    {
        $idRow = $this->getRowID();
        $this->checkEditPermission($idRow);
        $result = $this->controller->query->cloneRow($idRow);

        $result['html'] = 'ok';

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleShowAddFormAction()
    {
        $result = $this->controller->view->showEditForm();

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleSaveAddFormAction()
    {
        $result = $this->controller->query->insertRow(Input::all());
        $result['html'] = $this->controller->view->getRowHtml($result);

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleSaveEditFormAction()
    {
        $idRow = $this->getRowID();
        $this->checkEditPermission($idRow);

        $result = $this->controller->query->updateRow(Input::all());
        $result['html'] = $this->controller->view->getRowHtml($result);

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleShowEditFormAction()
    {
        $idRow = $this->getRowID();
        $this->checkEditPermission($idRow);

        $html = $this->controller->view->showEditForm($idRow);

        return Response::json([
            'html'   => $html,
            'status' => true,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleShowRevisionForm()
    {
        $idRow = $this->getRowID();
        $this->checkEditPermission($idRow);

        $html = $this->controller->view->showRevisionForm($idRow);

        return Response::json([
            'html'   => $html,
            'status' => true,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleShowViewsStatisic()
    {
        $idRow = $this->getRowID();
        $this->checkEditPermission($idRow);

        $html = $this->controller->view->showViewsStatistic($idRow);

        return Response::json([
            'html'   => $html,
            'status' => true,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleReturnRevisions()
    {
        $idRevision = request('id');
        $thisRevision = Revision::find($idRevision);

        $model = $thisRevision->revisionable_type;
        $key = $thisRevision->key;
        $modelObject = $model::find($thisRevision->revisionable_id);
        $modelObject->$key = $thisRevision->old_value;
        $modelObject->save();

        return Response::json(['status' => true]);
    }

    /**
     * @param $id
     */
    protected function checkEditPermission($id)
    {
    }

    /**
     * @return array|\Illuminate\Http\Request|string
     */
    private function getRowID()
    {
        if (request('id')) {
            return request('id');
        }

        throw new \RuntimeException('Undefined row id for action.');
    }

    /**
     * @return array
     */
    protected function handleShowList()
    {
        return [
           'showList' => $this->controller->view->showList(),
        ];
    }

    /**
     * @return array
     */
    protected function handleShowHtmlForeignDefinition()
    {
        return $this->controller->view->showHtmlForeignDefinition();
    }

    /**
     * @return array
     */
    protected function handleDeleteForeignDefinition()
    {
        return $this->controller->view->deleteForeignDefinition();
    }

    /**
     * @return array
     */
    protected function handleChangePositionDefinition()
    {
        return [
            'result' => $this->controller->view->changePositionDefinition(),
        ];
    }

    /**
     * @return array
     */
    protected function handleClearOrderBy()
    {
        return [
            'result' => $this->controller->query->clearOrderBy(),
        ];
    }

    protected function handleSearchAction()
    {
        $filters = request('filter', []);

        $newFilters = [];
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                if (isset($value['from']) && $value['from']) {
                    $newFilters[$key]['from'] = $value['from'];
                }
                if (isset($value['to']) && $value['to']) {
                    $newFilters[$key]['to'] = $value['to'];
                }
            } else {
                if ($value || $value === '0') {
                    $newFilters[$key] = $value;
                }
            }
        }

        if ($this->controller->hasCustomHandlerMethod('onPrepareSearchFilters')) {
            $this->controller->getCustomHandler()->onPrepareSearchFilters($newFilters);
        }

        $sessionPath = 'table_builder.'.$this->definitionName.'.filters';
        Session::put($sessionPath, $newFilters);
    }
}
