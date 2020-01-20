<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page_includes.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthentication()->applyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class convocacoeseventosPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Checkin Evento');
            $this->SetMenuLabel('Checkin Evento');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $this->dataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`convocacoeseventos`');
            $this->dataset->addFields(
                array(
                    new IntegerField('Id_Convocacao', true, true),
                    new IntegerField('Id_Evento'),
                    new IntegerField('Id_Voluntario'),
                    new StringField('St_VoluntarioCompareceu'),
                    new DateTimeField('Dt_Hr_Chegada'),
                    new DateTimeField('Dt_Hr_Saida')
                )
            );
            $this->dataset->AddLookupField('Id_Evento', 'eventos', new IntegerField('id_Evento'), new StringField('Ds_Evento', false, false, false, false, 'Id_Evento_Ds_Evento', 'Id_Evento_Ds_Evento_eventos'), 'Id_Evento_Ds_Evento_eventos');
            $this->dataset->AddLookupField('Id_Voluntario', 'vw_voluntarioevento', new StringField('Id_voluntario'), new StringField('descricao', false, false, false, false, 'Id_Voluntario_descricao', 'Id_Voluntario_descricao_vw_voluntarioevento'), 'Id_Voluntario_descricao_vw_voluntarioevento');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'Id_Convocacao', 'Id_Convocacao', 'Id Convocacao'),
                new FilterColumn($this->dataset, 'Id_Evento', 'Id_Evento_Ds_Evento', 'Evento'),
                new FilterColumn($this->dataset, 'Id_Voluntario', 'Id_Voluntario_descricao', 'CPF'),
                new FilterColumn($this->dataset, 'St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'St Voluntario Compareceu'),
                new FilterColumn($this->dataset, 'Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Dt Hr Chegada'),
                new FilterColumn($this->dataset, 'Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['Id_Convocacao'])
                ->addColumn($columns['Id_Evento'])
                ->addColumn($columns['Id_Voluntario'])
                ->addColumn($columns['St_VoluntarioCompareceu'])
                ->addColumn($columns['Dt_Hr_Chegada'])
                ->addColumn($columns['Dt_Hr_Saida']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('Id_Evento')
                ->setOptionsFor('Id_Voluntario')
                ->setOptionsFor('Dt_Hr_Chegada')
                ->setOptionsFor('Dt_Hr_Saida');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('id_convocacao_edit');
            
            $filterBuilder->addColumn(
                $columns['Id_Convocacao'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DynamicCombobox('id_evento_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_convocacoeseventos_Id_Evento_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_Evento', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_convocacoeseventos_Id_Evento_search');
            
            $filterBuilder->addColumn(
                $columns['Id_Evento'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DynamicCombobox('id_voluntario_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_convocacoeseventos_Id_Voluntario_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_Voluntario', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_convocacoeseventos_Id_Voluntario_search');
            
            $filterBuilder->addColumn(
                $columns['Id_Voluntario'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new ComboBox('st_voluntariocompareceu_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->addChoice('SIM', 'SIM');
            $main_editor->addChoice('NAO', 'NAO');
            $main_editor->SetAllowNullValue(false);
            
            $multi_value_select_editor = new MultiValueSelect('St_VoluntarioCompareceu');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('St_VoluntarioCompareceu');
            
            $filterBuilder->addColumn(
                $columns['St_VoluntarioCompareceu'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $text_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $text_editor,
                    FilterConditionOperator::BEGINS_WITH => $text_editor,
                    FilterConditionOperator::ENDS_WITH => $text_editor,
                    FilterConditionOperator::IS_LIKE => $text_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $text_editor,
                    FilterConditionOperator::IN => $multi_value_select_editor,
                    FilterConditionOperator::NOT_IN => $multi_value_select_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DateTimeEdit('dt_hr_chegada_edit', false, 'Y-m-d H:i:s');
            
            $filterBuilder->addColumn(
                $columns['Dt_Hr_Chegada'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::DATE_EQUALS => $main_editor,
                    FilterConditionOperator::DATE_DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::TODAY => null,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DateTimeEdit('dt_hr_saida_edit', false, 'Y-m-d H:i:s');
            
            $filterBuilder->addColumn(
                $columns['Dt_Hr_Saida'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::DATE_EQUALS => $main_editor,
                    FilterConditionOperator::DATE_DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::TODAY => null,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
            
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Id Convocacao', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario_descricao', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'St Voluntario Compareceu', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Dt Hr Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Id Convocacao', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario_descricao', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'St Voluntario Compareceu', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Dt Hr Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for Id_Evento field
            //
            $editor = new DynamicCombobox('id_evento_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'edit_convocacoeseventos_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Id_Voluntario field
            //
            $editor = new DynamicCombobox('id_voluntario_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('CPF', 'Id_Voluntario', 'Id_Voluntario_descricao', 'edit_convocacoeseventos_Id_Voluntario_search', $editor, $this->dataset, $lookupDataset, 'Id_voluntario', 'descricao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for St_VoluntarioCompareceu field
            //
            $editor = new ComboBox('st_voluntariocompareceu_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('SIM', 'SIM');
            $editor->addChoice('NAO', 'NAO');
            $editColumn = new CustomEditColumn('St Voluntario Compareceu', 'St_VoluntarioCompareceu', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Dt_Hr_Chegada field
            //
            $editor = new DateTimeEdit('dt_hr_chegada_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Dt Hr Chegada', 'Dt_Hr_Chegada', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Dt_Hr_Saida field
            //
            $editor = new DateTimeEdit('dt_hr_saida_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Dt Hr Saida', 'Dt_Hr_Saida', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for Id_Evento field
            //
            $editor = new DynamicCombobox('id_evento_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'multi_edit_convocacoeseventos_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Id_Voluntario field
            //
            $editor = new DynamicCombobox('id_voluntario_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('CPF', 'Id_Voluntario', 'Id_Voluntario_descricao', 'multi_edit_convocacoeseventos_Id_Voluntario_search', $editor, $this->dataset, $lookupDataset, 'Id_voluntario', 'descricao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for St_VoluntarioCompareceu field
            //
            $editor = new ComboBox('st_voluntariocompareceu_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('SIM', 'SIM');
            $editor->addChoice('NAO', 'NAO');
            $editColumn = new CustomEditColumn('St Voluntario Compareceu', 'St_VoluntarioCompareceu', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Dt_Hr_Chegada field
            //
            $editor = new DateTimeEdit('dt_hr_chegada_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Dt Hr Chegada', 'Dt_Hr_Chegada', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Dt_Hr_Saida field
            //
            $editor = new DateTimeEdit('dt_hr_saida_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Dt Hr Saida', 'Dt_Hr_Saida', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for Id_Evento field
            //
            $editor = new DynamicCombobox('id_evento_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'insert_convocacoeseventos_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Id_Voluntario field
            //
            $editor = new DynamicCombobox('id_voluntario_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('CPF', 'Id_Voluntario', 'Id_Voluntario_descricao', 'insert_convocacoeseventos_Id_Voluntario_search', $editor, $this->dataset, $lookupDataset, 'Id_voluntario', 'descricao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for St_VoluntarioCompareceu field
            //
            $editor = new ComboBox('st_voluntariocompareceu_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('SIM', 'SIM');
            $editor->addChoice('NAO', 'NAO');
            $editColumn = new CustomEditColumn('St Voluntario Compareceu', 'St_VoluntarioCompareceu', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Dt_Hr_Chegada field
            //
            $editor = new DateTimeEdit('dt_hr_chegada_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Dt Hr Chegada', 'Dt_Hr_Chegada', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Dt_Hr_Saida field
            //
            $editor = new DateTimeEdit('dt_hr_saida_edit', false, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Dt Hr Saida', 'Dt_Hr_Saida', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Id Convocacao', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario_descricao', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'St Voluntario Compareceu', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Dt Hr Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Id Convocacao', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario_descricao', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'St Voluntario Compareceu', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Dt Hr Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddExportColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Id Convocacao', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario_descricao', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'St Voluntario Compareceu', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Dt Hr Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddCompareColumn($column);
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(true);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(true);
            $result->setAllowCompare(true);
            $this->AddCompareHeaderColumns($result);
            $this->AddCompareColumns($result);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && true);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(true);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(true);
            $this->setAllowPrintSelectedRecords(true);
            $this->setExportListAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
            $this->setExportSelectedRecordsAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_convocacoeseventos_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_convocacoeseventos_Id_Voluntario_search', 'Id_voluntario', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_convocacoeseventos_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_convocacoeseventos_Id_Voluntario_search', 'Id_voluntario', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_convocacoeseventos_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_convocacoeseventos_Id_Voluntario_search', 'Id_voluntario', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new IntegerField('id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_convocacoeseventos_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor'),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_convocacoeseventos_Id_Voluntario_search', 'Id_voluntario', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
    
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }

    SetUpUserAuthorization();

    try
    {
        $Page = new convocacoeseventosPage("convocacoeseventos", "convocacoeseventos.php", GetCurrentUserPermissionSetForDataSource("convocacoeseventos"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("convocacoeseventos"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
