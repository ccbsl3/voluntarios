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
    
    
    
    class CHECKIN_EVENTOPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Checkin Evento');
            $this->SetMenuLabel('Checkin Evento');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $selectQuery = 'SELECT concat(replace(replace(v.id_voluntario,\'-\',\'\'),\'.\',\'\'),\' \',v.nm_voluntario) as descricao,v.Id_CCB,c.Ds_SubSetor,
            v.ID_FUNCAO1,v.ID_FUNCAO2,v.ID_FUNCAO3,
            cv.Id_Convocacao,cv.Id_Evento,v.Id_Voluntario,cv.St_VoluntarioCompareceu,cv.Dt_Hr_Chegada,cv.Dt_Hr_Saida,cv.ID_AUX
            FROM sl3.convocacoeseventos cv
            join sl3.cadvoluntarios v on cv.ID_AUX = v.ID_AUX
            join sl3.cadcongregacoes c on v.Id_CCB = c.Id_CCB';
            $insertQuery = array('INSERT INTO convocacoeseventos
            (Id_Evento,Id_Voluntario,St_VoluntarioCompareceu,Dt_Hr_Chegada,Dt_Hr_Saida,ID_AUX) values
            (\'1\',:Id_Voluntario,\'SIM\',NOW(),NOW(),:ID_AUX)');
            $updateQuery = array('UPDATE convocacoeseventos
            SET Id_Evento = :Id_Evento,
            Id_Voluntario = :Id_Voluntario,
            ID_AUX = :ID_AUX
            WHERE Id_Convocacao = :OLD_Id_Convocacao');
            $deleteQuery = array('DELETE FROM convocacoeseventos WHERE Id_Convocacao = :OLD_Id_Convocacao');
            $this->dataset = new QueryDataset(
              MySqlIConnectionFactory::getInstance(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'CHECKIN_EVENTO');
            $this->dataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_CCB'),
                    new StringField('Ds_SubSetor'),
                    new IntegerField('ID_FUNCAO1'),
                    new IntegerField('ID_FUNCAO2'),
                    new IntegerField('ID_FUNCAO3'),
                    new IntegerField('Id_Convocacao', true, true, true),
                    new IntegerField('Id_Evento'),
                    new StringField('Id_Voluntario'),
                    new StringField('St_VoluntarioCompareceu'),
                    new DateTimeField('Dt_Hr_Chegada'),
                    new DateTimeField('Dt_Hr_Saida'),
                    new IntegerField('ID_AUX')
                )
            );
            $this->dataset->AddLookupField('Id_CCB', 'cadcongregacoes', new StringField('Id_CCB'), new StringField('Ds_CCB', false, false, false, false, 'Id_CCB_Ds_CCB', 'Id_CCB_Ds_CCB_cadcongregacoes'), 'Id_CCB_Ds_CCB_cadcongregacoes');
            $this->dataset->AddLookupField('Id_Evento', 'eventos', new IntegerField('id_Evento'), new StringField('Ds_Evento', false, false, false, false, 'Id_Evento_Ds_Evento', 'Id_Evento_Ds_Evento_eventos'), 'Id_Evento_Ds_Evento_eventos');
            $this->dataset->AddLookupField('ID_AUX', 'vw_voluntarioevento', new IntegerField('id_aux'), new StringField('descricao', false, false, false, false, 'ID_AUX_descricao', 'ID_AUX_descricao_vw_voluntarioevento'), 'ID_AUX_descricao_vw_voluntarioevento');
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
                new FilterColumn($this->dataset, 'descricao', 'descricao', 'Voluntários'),
                new FilterColumn($this->dataset, 'Id_Voluntario', 'Id_Voluntario', 'CPF'),
                new FilterColumn($this->dataset, 'Ds_SubSetor', 'Ds_SubSetor', 'Ds Sub Setor'),
                new FilterColumn($this->dataset, 'Id_CCB', 'Id_CCB_Ds_CCB', 'CCB'),
                new FilterColumn($this->dataset, 'Id_Evento', 'Id_Evento_Ds_Evento', 'Evento'),
                new FilterColumn($this->dataset, 'St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'Comparecimento'),
                new FilterColumn($this->dataset, 'Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada'),
                new FilterColumn($this->dataset, 'Id_Convocacao', 'Id_Convocacao', 'Número de Convocação'),
                new FilterColumn($this->dataset, 'ID_FUNCAO1', 'ID_FUNCAO1', 'ID FUNCAO1'),
                new FilterColumn($this->dataset, 'ID_FUNCAO2', 'ID_FUNCAO2', 'ID FUNCAO2'),
                new FilterColumn($this->dataset, 'ID_FUNCAO3', 'ID_FUNCAO3', 'ID FUNCAO3'),
                new FilterColumn($this->dataset, 'Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida'),
                new FilterColumn($this->dataset, 'ID_AUX', 'ID_AUX_descricao', 'Voluntário')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['descricao'])
                ->addColumn($columns['Id_Voluntario'])
                ->addColumn($columns['Ds_SubSetor'])
                ->addColumn($columns['Id_CCB'])
                ->addColumn($columns['Id_Evento'])
                ->addColumn($columns['St_VoluntarioCompareceu'])
                ->addColumn($columns['Dt_Hr_Chegada'])
                ->addColumn($columns['Id_Convocacao'])
                ->addColumn($columns['ID_FUNCAO1'])
                ->addColumn($columns['ID_FUNCAO2'])
                ->addColumn($columns['ID_FUNCAO3'])
                ->addColumn($columns['Dt_Hr_Saida'])
                ->addColumn($columns['ID_AUX']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('Id_CCB')
                ->setOptionsFor('Id_Evento')
                ->setOptionsFor('Dt_Hr_Chegada');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new TextEdit('descricao_edit');
            
            $filterBuilder->addColumn(
                $columns['descricao'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('id_voluntario_edit');
            
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
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new TextEdit('ds_subsetor_edit');
            
            $filterBuilder->addColumn(
                $columns['Ds_SubSetor'],
                array(
                    FilterConditionOperator::EQUALS => $main_editor,
                    FilterConditionOperator::DOES_NOT_EQUAL => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN => $main_editor,
                    FilterConditionOperator::IS_GREATER_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN => $main_editor,
                    FilterConditionOperator::IS_LESS_THAN_OR_EQUAL_TO => $main_editor,
                    FilterConditionOperator::IS_BETWEEN => $main_editor,
                    FilterConditionOperator::IS_NOT_BETWEEN => $main_editor,
                    FilterConditionOperator::CONTAINS => $main_editor,
                    FilterConditionOperator::DOES_NOT_CONTAIN => $main_editor,
                    FilterConditionOperator::BEGINS_WITH => $main_editor,
                    FilterConditionOperator::ENDS_WITH => $main_editor,
                    FilterConditionOperator::IS_LIKE => $main_editor,
                    FilterConditionOperator::IS_NOT_LIKE => $main_editor,
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new DynamicCombobox('id_ccb_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_Id_CCB_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_CCB', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_Id_CCB_search');
            
            $text_editor = new TextEdit('Id_CCB');
            
            $filterBuilder->addColumn(
                $columns['Id_CCB'],
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
            
            $main_editor = new DynamicCombobox('id_evento_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_Id_Evento_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_Evento', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_Id_Evento_search');
            
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
            
            $main_editor = new ComboBox('st_voluntariocompareceu_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->addChoice('SIM', 'SIM');
            $main_editor->addChoice('NÃO', 'NÃO');
            $main_editor->addChoice('', 'NÃO');
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
            
            $main_editor = new DateTimeEdit('dt_hr_chegada_edit', false, 'd.m.Y H:i:s');
            
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
            
            $main_editor = new SpinEdit('id_convocacao_edit');
            
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
            
            $main_editor = new SpinEdit('id_funcao1_edit');
            
            $filterBuilder->addColumn(
                $columns['ID_FUNCAO1'],
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
            
            $main_editor = new SpinEdit('id_funcao2_edit');
            
            $filterBuilder->addColumn(
                $columns['ID_FUNCAO2'],
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
            
            $main_editor = new SpinEdit('id_funcao3_edit');
            
            $filterBuilder->addColumn(
                $columns['ID_FUNCAO3'],
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
            
            $main_editor = new DynamicCombobox('id_aux_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_ID_AUX_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('ID_AUX', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_ID_AUX_search');
            
            $filterBuilder->addColumn(
                $columns['ID_AUX'],
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
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Voluntários', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Id_Voluntario field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario', 'CPF', $this->dataset);
            $column->setNullLabel('');
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_SubSetor field
            //
            $column = new TextViewColumn('Ds_SubSetor', 'Ds_SubSetor', 'Ds Sub Setor', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('Id_CCB', 'Id_CCB_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
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
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'Comparecimento', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y H:i:s');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Número de Convocação', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Voluntários', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Id_Voluntario field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario', 'CPF', $this->dataset);
            $column->setNullLabel('');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_SubSetor field
            //
            $column = new TextViewColumn('Ds_SubSetor', 'Ds_SubSetor', 'Ds Sub Setor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('Id_CCB', 'Id_CCB_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'Comparecimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y H:i:s');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Número de Convocação', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
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
                    new StringField('Id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento'),
                    new StringField('anexo_evento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'edit_CHECKIN_EVENTO_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ID_AUX field
            //
            $editor = new DynamicCombobox('id_aux_edit', $this->CreateLinkBuilder());
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
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Voluntário', 'ID_AUX', 'ID_AUX_descricao', 'edit_CHECKIN_EVENTO_ID_AUX_search', $editor, $this->dataset, $lookupDataset, 'id_aux', 'descricao', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
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
                    new StringField('Id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento'),
                    new StringField('anexo_evento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'multi_edit_CHECKIN_EVENTO_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for ID_AUX field
            //
            $editor = new DynamicCombobox('id_aux_edit', $this->CreateLinkBuilder());
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
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Voluntário', 'ID_AUX', 'ID_AUX_descricao', 'multi_edit_CHECKIN_EVENTO_ID_AUX_search', $editor, $this->dataset, $lookupDataset, 'id_aux', 'descricao', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for ID_AUX field
            //
            $editor = new DynamicCombobox('id_aux_edit', $this->CreateLinkBuilder());
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
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Voluntário', 'ID_AUX', 'ID_AUX_descricao', 'insert_CHECKIN_EVENTO_ID_AUX_search', $editor, $this->dataset, $lookupDataset, 'id_aux', 'descricao', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
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
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Voluntários', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Id_Voluntario field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario', 'CPF', $this->dataset);
            $column->setNullLabel('');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_SubSetor field
            //
            $column = new TextViewColumn('Ds_SubSetor', 'Ds_SubSetor', 'Ds Sub Setor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('Id_CCB', 'Id_CCB_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'Comparecimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y H:i:s');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Número de Convocação', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for ID_FUNCAO1 field
            //
            $column = new NumberViewColumn('ID_FUNCAO1', 'ID_FUNCAO1', 'ID FUNCAO1', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for ID_FUNCAO2 field
            //
            $column = new NumberViewColumn('ID_FUNCAO2', 'ID_FUNCAO2', 'ID FUNCAO2', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for ID_FUNCAO3 field
            //
            $column = new NumberViewColumn('ID_FUNCAO3', 'ID_FUNCAO3', 'ID FUNCAO3', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddPrintColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('ID_AUX', 'ID_AUX_descricao', 'Voluntário', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Voluntários', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Id_Voluntario field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario', 'CPF', $this->dataset);
            $column->setNullLabel('');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_SubSetor field
            //
            $column = new TextViewColumn('Ds_SubSetor', 'Ds_SubSetor', 'Ds Sub Setor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('Id_CCB', 'Id_CCB_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'Comparecimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y H:i:s');
            $grid->AddExportColumn($column);
            
            //
            // View column for Id_Convocacao field
            //
            $column = new NumberViewColumn('Id_Convocacao', 'Id_Convocacao', 'Número de Convocação', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for ID_FUNCAO1 field
            //
            $column = new NumberViewColumn('ID_FUNCAO1', 'ID_FUNCAO1', 'ID FUNCAO1', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for ID_FUNCAO2 field
            //
            $column = new NumberViewColumn('ID_FUNCAO2', 'ID_FUNCAO2', 'ID FUNCAO2', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for ID_FUNCAO3 field
            //
            $column = new NumberViewColumn('ID_FUNCAO3', 'ID_FUNCAO3', 'ID FUNCAO3', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddExportColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('ID_AUX', 'ID_AUX_descricao', 'Voluntário', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Voluntários', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Id_Voluntario field
            //
            $column = new TextViewColumn('Id_Voluntario', 'Id_Voluntario', 'CPF', $this->dataset);
            $column->setNullLabel('');
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_SubSetor field
            //
            $column = new TextViewColumn('Ds_SubSetor', 'Ds_SubSetor', 'Ds Sub Setor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('Id_CCB', 'Id_CCB_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for St_VoluntarioCompareceu field
            //
            $column = new TextViewColumn('St_VoluntarioCompareceu', 'St_VoluntarioCompareceu', 'Comparecimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d.m.Y H:i:s');
            $grid->AddCompareColumn($column);
            
            //
            // View column for ID_FUNCAO1 field
            //
            $column = new NumberViewColumn('ID_FUNCAO1', 'ID_FUNCAO1', 'ID FUNCAO1', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for ID_FUNCAO2 field
            //
            $column = new NumberViewColumn('ID_FUNCAO2', 'ID_FUNCAO2', 'ID FUNCAO2', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for ID_FUNCAO3 field
            //
            $column = new NumberViewColumn('ID_FUNCAO3', 'ID_FUNCAO3', 'ID FUNCAO3', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddCompareColumn($column);
            
            //
            // View column for Dt_Hr_Saida field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Saida', 'Dt_Hr_Saida', 'Dt Hr Saida', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddCompareColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('ID_AUX', 'ID_AUX_descricao', 'Voluntário', $this->dataset);
            $column->SetOrderable(true);
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
                '`vw_voluntarioevento`');
            $lookupDataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('Id_voluntario'),
                    new StringField('NM_VOLUNTARIO'),
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CHECKIN_EVENTO_ID_AUX_search', 'id_aux', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadcongregacoes`');
            $lookupDataset->addFields(
                array(
                    new StringField('Id_CCB', true, true),
                    new StringField('Ds_CCB'),
                    new StringField('Ds_SubSetor'),
                    new StringField('Ds_Endereco_CCB'),
                    new StringField('Cep_CCB'),
                    new StringField('tel_CCB'),
                    new StringField('Dia_Culto_1'),
                    new StringField('Hora_Culto_1'),
                    new StringField('Dia_Culto_2'),
                    new StringField('Hora_Culto_2'),
                    new StringField('Dia_Culto_3'),
                    new StringField('Hora_Culto_3'),
                    new StringField('Dia_Culto_4'),
                    new StringField('Hora_Culto_4'),
                    new StringField('Dia_RJM'),
                    new StringField('Hora_RJM'),
                    new StringField('Dia_Ensaio'),
                    new StringField('Hora_Ensaio'),
                    new StringField('Semana_ensaio')
                )
            );
            $lookupDataset->setOrderByField('Ds_CCB', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_Id_CCB_search', 'Id_CCB', 'Ds_CCB', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new StringField('Id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento'),
                    new StringField('anexo_evento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
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
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_ID_AUX_search', 'id_aux', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new StringField('Id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento'),
                    new StringField('anexo_evento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CHECKIN_EVENTO_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
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
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CHECKIN_EVENTO_ID_AUX_search', 'id_aux', 'descricao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`eventos`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id_Evento', true, true),
                    new StringField('Id_CCB'),
                    new StringField('Ds_Evento'),
                    new DateTimeField('Dt_Evento'),
                    new TimeField('Hr_Inicio'),
                    new TimeField('Hr_Termino'),
                    new StringField('Ds_AtaEvento'),
                    new StringField('anexo_evento')
                )
            );
            $lookupDataset->setOrderByField('Ds_Evento', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CHECKIN_EVENTO_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
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
                    new StringField('ds_subsetor', true),
                    new StringField('Id_CCB'),
                    new IntegerField('id_aux', true)
                )
            );
            $lookupDataset->setOrderByField('descricao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CHECKIN_EVENTO_ID_AUX_search', 'id_aux', 'descricao', null, 20);
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
        $Page = new CHECKIN_EVENTOPage("CHECKIN_EVENTO", "CHECKIN_EVENTO.php", GetCurrentUserPermissionSetForDataSource("CHECKIN_EVENTO"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("CHECKIN_EVENTO"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
