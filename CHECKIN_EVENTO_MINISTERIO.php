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
    
    
    
    class CHECKIN_EVENTO_MINISTERIOPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Checkin Evento Ministério');
            $this->SetMenuLabel('Checkin Evento Ministério');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $selectQuery = 'SELECT IFNULL(cv.Id_irmaoministerio,cm.Id_irmaoministerio) Id_irmaoministerio,
            cv.Id_Convocacao_Ministerio,
            cv.Id_Evento,
            cv.Dt_Hr_Chegada,
            IFNULL(cv.NomeCompleto,cm.NomeCompleto) NomeCompleto,
            IFNULL(cv.Ministerio,cm.Ministerio) Ministerio,
            IFNULL(cv.Telefone,(IFNULL(cm.TelefoneCelular,cm.TelefoneFixo))) Telefone,
            IFNULL(cv.SubSetor,cm.SubSetor) SubSetor,
            IFNULL(cv.ComumCongregacao,cm.ComumCongregacao) ComumCongregacao,
            cv.Id_Estado,
            cv.Id_Cidade
            FROM convocacoeseventosministerio cv
            left JOIN cadministerio cm on cv.Id_irmaoministerio = cm.Id_irmaoministerio';
            $insertQuery = array('INSERT INTO convocacoeseventosministerio 
            (Id_irmaoministerio,Id_Evento,Dt_Hr_Chegada,NomeCompleto,Ministerio,Telefone,
            SubSetor,ComumCongregacao,Id_Estado,Id_Cidade) VALUES 
            (:Id_irmaoministerio,\'1\',NOW(),UPPER(:NomeCompleto),:Ministerio,:Telefone,
            UPPER(:SubSetor),UPPER(:ComumCongregacao),:Id_Estado,:Id_Cidade)');
            $updateQuery = array('UPDATE convocacoeseventosministerio 
            SET Id_irmaoministerio = :Id_irmaoministerio,
            Id_Evento = :Id_Evento,
            NomeCompleto = UPPER(:NomeCompleto),
            Ministerio = :Ministerio,
            Telefone = :Telefone,
            SubSetor = UPPER(:SubSetor),
            ComumCongregacao = UPPER(:ComumCongregacao),
            Id_Estado = :Id_Estado,
            Id_Cidade = :Id_Cidade
            WHERE Id_Convocacao_Ministerio = :OLD_Id_Convocacao_Ministerio');
            $deleteQuery = array('DELETE convocacoeseventosministerio WHERE Id_Convocacao_Ministerio = :OLD_Id_Convocacao_Ministerio');
            $this->dataset = new QueryDataset(
              MySqlIConnectionFactory::getInstance(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'CHECKIN_EVENTO_MINISTERIO');
            $this->dataset->addFields(
                array(
                    new StringField('Id_irmaoministerio'),
                    new IntegerField('Id_Convocacao_Ministerio', true, true, true),
                    new IntegerField('Id_Evento'),
                    new DateTimeField('Dt_Hr_Chegada'),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('Telefone'),
                    new StringField('SubSetor'),
                    new StringField('ComumCongregacao'),
                    new IntegerField('Id_Estado'),
                    new IntegerField('Id_Cidade')
                )
            );
            $this->dataset->AddLookupField('Id_irmaoministerio', 'cadministerio', new IntegerField('Id_irmaoministerio'), new StringField('NomeCompleto', false, false, false, false, 'Id_irmaoministerio_NomeCompleto', 'Id_irmaoministerio_NomeCompleto_cadministerio'), 'Id_irmaoministerio_NomeCompleto_cadministerio');
            $this->dataset->AddLookupField('Id_Evento', 'eventos', new IntegerField('id_Evento'), new StringField('Ds_Evento', false, false, false, false, 'Id_Evento_Ds_Evento', 'Id_Evento_Ds_Evento_eventos'), 'Id_Evento_Ds_Evento_eventos');
            $this->dataset->AddLookupField('Id_Estado', 'estado', new IntegerField('id'), new StringField('nome', false, false, false, false, 'Id_Estado_nome', 'Id_Estado_nome_estado'), 'Id_Estado_nome_estado');
            $this->dataset->AddLookupField('Id_Cidade', 'cidade', new IntegerField('id'), new StringField('nome', false, false, false, false, 'Id_Cidade_nome', 'Id_Cidade_nome_cidade'), 'Id_Cidade_nome_cidade');
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
                new FilterColumn($this->dataset, 'Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'Irmão Ministério'),
                new FilterColumn($this->dataset, 'Id_Convocacao_Ministerio', 'Id_Convocacao_Ministerio', 'Id Convocacao Ministerio'),
                new FilterColumn($this->dataset, 'Id_Evento', 'Id_Evento_Ds_Evento', 'Evento'),
                new FilterColumn($this->dataset, 'NomeCompleto', 'NomeCompleto', 'Nome Completo'),
                new FilterColumn($this->dataset, 'Ministerio', 'Ministerio', 'Ministério'),
                new FilterColumn($this->dataset, 'Telefone', 'Telefone', 'Telefone'),
                new FilterColumn($this->dataset, 'SubSetor', 'SubSetor', 'SubSetor'),
                new FilterColumn($this->dataset, 'ComumCongregacao', 'ComumCongregacao', 'Comum Congregação'),
                new FilterColumn($this->dataset, 'Id_Estado', 'Id_Estado_nome', 'Estado'),
                new FilterColumn($this->dataset, 'Id_Cidade', 'Id_Cidade_nome', 'Cidade'),
                new FilterColumn($this->dataset, 'Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['Id_irmaoministerio'])
                ->addColumn($columns['Id_Convocacao_Ministerio'])
                ->addColumn($columns['Id_Evento'])
                ->addColumn($columns['NomeCompleto'])
                ->addColumn($columns['Ministerio'])
                ->addColumn($columns['Telefone'])
                ->addColumn($columns['SubSetor'])
                ->addColumn($columns['ComumCongregacao'])
                ->addColumn($columns['Id_Estado'])
                ->addColumn($columns['Id_Cidade'])
                ->addColumn($columns['Dt_Hr_Chegada']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('Id_Evento')
                ->setOptionsFor('Id_Estado')
                ->setOptionsFor('Id_Cidade')
                ->setOptionsFor('Dt_Hr_Chegada');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
            $main_editor = new DynamicCombobox('id_irmaoministerio_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_irmaoministerio', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search');
            
            $text_editor = new TextEdit('Id_irmaoministerio');
            
            $filterBuilder->addColumn(
                $columns['Id_irmaoministerio'],
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
            
            $main_editor = new SpinEdit('id_convocacao_ministerio_edit');
            
            $filterBuilder->addColumn(
                $columns['Id_Convocacao_Ministerio'],
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
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_Evento', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search');
            
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
            
            $main_editor = new TextEdit('nomecompleto_edit');
            
            $filterBuilder->addColumn(
                $columns['NomeCompleto'],
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
            
            $main_editor = new ComboBox('ministerio_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->addChoice('Ancião', 'Ancião');
            $main_editor->addChoice('Cooperador Jovens e Menores', 'Cooperador Jovens e Menores');
            $main_editor->addChoice('Cooperador Ofício', 'Cooperador Ofício');
            $main_editor->addChoice('Diácono', 'Diácono');
            $main_editor->addChoice('Encarregado Regional', 'Encarregado Regional');
            $main_editor->addChoice('Encarregado Local', 'Encarregado Local');
            $main_editor->addChoice('Examinadora', 'Examinadora');
            $main_editor->addChoice('Piedade', 'Piedade');
            $main_editor->SetAllowNullValue(false);
            
            $multi_value_select_editor = new MultiValueSelect('Ministerio');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('Ministerio');
            
            $filterBuilder->addColumn(
                $columns['Ministerio'],
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
            
            $main_editor = new MaskedEdit('telefone_edit', '(99) 9 9999-9999');
            
            $text_editor = new TextEdit('Telefone');
            
            $filterBuilder->addColumn(
                $columns['Telefone'],
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
                    FilterConditionOperator::IS_BLANK => null,
                    FilterConditionOperator::IS_NOT_BLANK => null
                )
            );
            
            $main_editor = new ComboBox('subsetor_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $main_editor->addChoice('GUAIANAZES', 'GUAIANAZES');
            $main_editor->addChoice('ITAQUERA', 'ITAQUERA');
            $main_editor->addChoice('SALETE', 'SALETE');
            $main_editor->addChoice('SAO MIGUEL', 'SAO MIGUEL');
            $main_editor->addChoice('TIRADENTES', 'TIRADENTES');
            $main_editor->addChoice('OUTROS', 'OUTROS');
            $main_editor->SetAllowNullValue(false);
            
            $multi_value_select_editor = new MultiValueSelect('SubSetor');
            $multi_value_select_editor->setChoices($main_editor->getChoices());
            
            $text_editor = new TextEdit('SubSetor');
            
            $filterBuilder->addColumn(
                $columns['SubSetor'],
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
            
            $main_editor = new TextEdit('comumcongregacao_edit');
            
            $filterBuilder->addColumn(
                $columns['ComumCongregacao'],
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
            
            $main_editor = new DynamicCombobox('id_estado_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_Estado', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search');
            
            $filterBuilder->addColumn(
                $columns['Id_Estado'],
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
            
            $main_editor = new DynamicCombobox('id_cidade_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('Id_Cidade', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search');
            
            $filterBuilder->addColumn(
                $columns['Id_Cidade'],
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
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('NomeCompleto', 'NomeCompleto', 'Nome Completo', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ministerio field
            //
            $column = new TextViewColumn('Ministerio', 'Ministerio', 'Ministério', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Telefone field
            //
            $column = new TextViewColumn('Telefone', 'Telefone', 'Telefone', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for SubSetor field
            //
            $column = new TextViewColumn('SubSetor', 'SubSetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for ComumCongregacao field
            //
            $column = new TextViewColumn('ComumCongregacao', 'ComumCongregacao', 'Comum Congregação', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Estado', 'Id_Estado_nome', 'Estado', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Cidade', 'Id_Cidade_nome', 'Cidade', $this->dataset);
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
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for Ds_Evento field
            //
            $column = new TextViewColumn('Id_Evento', 'Id_Evento_Ds_Evento', 'Evento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('NomeCompleto', 'NomeCompleto', 'Nome Completo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ministerio field
            //
            $column = new TextViewColumn('Ministerio', 'Ministerio', 'Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Telefone field
            //
            $column = new TextViewColumn('Telefone', 'Telefone', 'Telefone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for SubSetor field
            //
            $column = new TextViewColumn('SubSetor', 'SubSetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for ComumCongregacao field
            //
            $column = new TextViewColumn('ComumCongregacao', 'ComumCongregacao', 'Comum Congregação', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Estado', 'Id_Estado_nome', 'Estado', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Cidade', 'Id_Cidade_nome', 'Cidade', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for Id_irmaoministerio field
            //
            $editor = new DynamicCombobox('id_irmaoministerio_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Irmão Ministério', 'Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'edit_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', $editor, $this->dataset, $lookupDataset, 'Id_irmaoministerio', 'NomeCompleto', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
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
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'edit_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for NomeCompleto field
            //
            $editor = new TextEdit('nomecompleto_edit');
            $editColumn = new CustomEditColumn('Nome Completo', 'NomeCompleto', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Ministerio field
            //
            $editor = new ComboBox('ministerio_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('Ancião', 'Ancião');
            $editor->addChoice('Cooperador Jovens e Menores', 'Cooperador Jovens e Menores');
            $editor->addChoice('Cooperador Ofício', 'Cooperador Ofício');
            $editor->addChoice('Diácono', 'Diácono');
            $editor->addChoice('Encarregado Regional', 'Encarregado Regional');
            $editor->addChoice('Encarregado Local', 'Encarregado Local');
            $editor->addChoice('Examinadora', 'Examinadora');
            $editor->addChoice('Piedade', 'Piedade');
            $editColumn = new CustomEditColumn('Ministério', 'Ministerio', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Telefone field
            //
            $editor = new MaskedEdit('telefone_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone', 'Telefone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for SubSetor field
            //
            $editor = new ComboBox('subsetor_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('GUAIANAZES', 'GUAIANAZES');
            $editor->addChoice('ITAQUERA', 'ITAQUERA');
            $editor->addChoice('SALETE', 'SALETE');
            $editor->addChoice('SAO MIGUEL', 'SAO MIGUEL');
            $editor->addChoice('TIRADENTES', 'TIRADENTES');
            $editor->addChoice('OUTROS', 'OUTROS');
            $editColumn = new CustomEditColumn('SubSetor', 'SubSetor', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ComumCongregacao field
            //
            $editor = new TextEdit('comumcongregacao_edit');
            $editColumn = new CustomEditColumn('Comum Congregação', 'ComumCongregacao', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Id_Estado field
            //
            $editor = new DynamicCombobox('id_estado_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Estado', 'Id_Estado', 'Id_Estado_nome', 'edit_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', $editor, $this->dataset, $lookupDataset, 'id', 'nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for Id_Cidade field
            //
            $editor = new DynamicCombobox('id_cidade_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cidade', 'Id_Cidade', 'Id_Cidade_nome', 'edit_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', $editor, $this->dataset, $lookupDataset, 'id', 'nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for Id_irmaoministerio field
            //
            $editor = new DynamicCombobox('id_irmaoministerio_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Irmão Ministério', 'Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', $editor, $this->dataset, $lookupDataset, 'Id_irmaoministerio', 'NomeCompleto', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
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
            $editColumn = new DynamicLookupEditColumn('Evento', 'Id_Evento', 'Id_Evento_Ds_Evento', 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search', $editor, $this->dataset, $lookupDataset, 'id_Evento', 'Ds_Evento', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for NomeCompleto field
            //
            $editor = new TextEdit('nomecompleto_edit');
            $editColumn = new CustomEditColumn('Nome Completo', 'NomeCompleto', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Ministerio field
            //
            $editor = new ComboBox('ministerio_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('Ancião', 'Ancião');
            $editor->addChoice('Cooperador Jovens e Menores', 'Cooperador Jovens e Menores');
            $editor->addChoice('Cooperador Ofício', 'Cooperador Ofício');
            $editor->addChoice('Diácono', 'Diácono');
            $editor->addChoice('Encarregado Regional', 'Encarregado Regional');
            $editor->addChoice('Encarregado Local', 'Encarregado Local');
            $editor->addChoice('Examinadora', 'Examinadora');
            $editor->addChoice('Piedade', 'Piedade');
            $editColumn = new CustomEditColumn('Ministério', 'Ministerio', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Telefone field
            //
            $editor = new MaskedEdit('telefone_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone', 'Telefone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for SubSetor field
            //
            $editor = new ComboBox('subsetor_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('GUAIANAZES', 'GUAIANAZES');
            $editor->addChoice('ITAQUERA', 'ITAQUERA');
            $editor->addChoice('SALETE', 'SALETE');
            $editor->addChoice('SAO MIGUEL', 'SAO MIGUEL');
            $editor->addChoice('TIRADENTES', 'TIRADENTES');
            $editor->addChoice('OUTROS', 'OUTROS');
            $editColumn = new CustomEditColumn('SubSetor', 'SubSetor', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for ComumCongregacao field
            //
            $editor = new TextEdit('comumcongregacao_edit');
            $editColumn = new CustomEditColumn('Comum Congregação', 'ComumCongregacao', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Id_Estado field
            //
            $editor = new DynamicCombobox('id_estado_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Estado', 'Id_Estado', 'Id_Estado_nome', 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', $editor, $this->dataset, $lookupDataset, 'id', 'nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for Id_Cidade field
            //
            $editor = new DynamicCombobox('id_cidade_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cidade', 'Id_Cidade', 'Id_Cidade_nome', 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', $editor, $this->dataset, $lookupDataset, 'id', 'nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for Id_irmaoministerio field
            //
            $editor = new DynamicCombobox('id_irmaoministerio_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Irmão Ministério', 'Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'insert_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', $editor, $this->dataset, $lookupDataset, 'Id_irmaoministerio', 'NomeCompleto', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for NomeCompleto field
            //
            $editor = new TextEdit('nomecompleto_edit');
            $editColumn = new CustomEditColumn('Nome Completo', 'NomeCompleto', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Ministerio field
            //
            $editor = new ComboBox('ministerio_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('Ancião', 'Ancião');
            $editor->addChoice('Cooperador Jovens e Menores', 'Cooperador Jovens e Menores');
            $editor->addChoice('Cooperador Ofício', 'Cooperador Ofício');
            $editor->addChoice('Diácono', 'Diácono');
            $editor->addChoice('Encarregado Regional', 'Encarregado Regional');
            $editor->addChoice('Encarregado Local', 'Encarregado Local');
            $editor->addChoice('Examinadora', 'Examinadora');
            $editor->addChoice('Piedade', 'Piedade');
            $editColumn = new CustomEditColumn('Ministério', 'Ministerio', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Telefone field
            //
            $editor = new MaskedEdit('telefone_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone', 'Telefone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for SubSetor field
            //
            $editor = new ComboBox('subsetor_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->addChoice('GUAIANAZES', 'GUAIANAZES');
            $editor->addChoice('ITAQUERA', 'ITAQUERA');
            $editor->addChoice('SALETE', 'SALETE');
            $editor->addChoice('SAO MIGUEL', 'SAO MIGUEL');
            $editor->addChoice('TIRADENTES', 'TIRADENTES');
            $editor->addChoice('OUTROS', 'OUTROS');
            $editColumn = new CustomEditColumn('SubSetor', 'SubSetor', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ComumCongregacao field
            //
            $editor = new TextEdit('comumcongregacao_edit');
            $editColumn = new CustomEditColumn('Comum Congregação', 'ComumCongregacao', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Id_Estado field
            //
            $editor = new DynamicCombobox('id_estado_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Estado', 'Id_Estado', 'Id_Estado_nome', 'insert_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', $editor, $this->dataset, $lookupDataset, 'id', 'nome', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for Id_Cidade field
            //
            $editor = new DynamicCombobox('id_cidade_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cidade', 'Id_Cidade', 'Id_Cidade_nome', 'insert_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', $editor, $this->dataset, $lookupDataset, 'id', 'nome', '');
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
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'Irmão Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Id_Convocacao_Ministerio field
            //
            $column = new NumberViewColumn('Id_Convocacao_Ministerio', 'Id_Convocacao_Ministerio', 'Id Convocacao Ministerio', $this->dataset);
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
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('NomeCompleto', 'NomeCompleto', 'Nome Completo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ministerio field
            //
            $column = new TextViewColumn('Ministerio', 'Ministerio', 'Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Telefone field
            //
            $column = new TextViewColumn('Telefone', 'Telefone', 'Telefone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for SubSetor field
            //
            $column = new TextViewColumn('SubSetor', 'SubSetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for ComumCongregacao field
            //
            $column = new TextViewColumn('ComumCongregacao', 'ComumCongregacao', 'Comum Congregação', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Estado', 'Id_Estado_nome', 'Estado', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Cidade', 'Id_Cidade_nome', 'Cidade', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'Irmão Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Id_Convocacao_Ministerio field
            //
            $column = new NumberViewColumn('Id_Convocacao_Ministerio', 'Id_Convocacao_Ministerio', 'Id Convocacao Ministerio', $this->dataset);
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
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('NomeCompleto', 'NomeCompleto', 'Nome Completo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ministerio field
            //
            $column = new TextViewColumn('Ministerio', 'Ministerio', 'Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Telefone field
            //
            $column = new TextViewColumn('Telefone', 'Telefone', 'Telefone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for SubSetor field
            //
            $column = new TextViewColumn('SubSetor', 'SubSetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for ComumCongregacao field
            //
            $column = new TextViewColumn('ComumCongregacao', 'ComumCongregacao', 'Comum Congregação', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Estado', 'Id_Estado_nome', 'Estado', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Cidade', 'Id_Cidade_nome', 'Cidade', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('Id_irmaoministerio', 'Id_irmaoministerio_NomeCompleto', 'Irmão Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Id_Convocacao_Ministerio field
            //
            $column = new NumberViewColumn('Id_Convocacao_Ministerio', 'Id_Convocacao_Ministerio', 'Id Convocacao Ministerio', $this->dataset);
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
            // View column for NomeCompleto field
            //
            $column = new TextViewColumn('NomeCompleto', 'NomeCompleto', 'Nome Completo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ministerio field
            //
            $column = new TextViewColumn('Ministerio', 'Ministerio', 'Ministério', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Telefone field
            //
            $column = new TextViewColumn('Telefone', 'Telefone', 'Telefone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for SubSetor field
            //
            $column = new TextViewColumn('SubSetor', 'SubSetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for ComumCongregacao field
            //
            $column = new TextViewColumn('ComumCongregacao', 'ComumCongregacao', 'Comum Congregação', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Estado', 'Id_Estado_nome', 'Estado', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('Id_Cidade', 'Id_Cidade_nome', 'Cidade', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Dt_Hr_Chegada field
            //
            $column = new DateTimeViewColumn('Dt_Hr_Chegada', 'Dt_Hr_Chegada', 'Hora Chegada', $this->dataset);
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
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', 'Id_irmaoministerio', 'NomeCompleto', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', 'Id_irmaoministerio', 'NomeCompleto', null, 20);
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', 'Id_irmaoministerio', 'NomeCompleto', null, 20);
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cadministerio`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_irmaoministerio', true, true),
                    new StringField('NomeCompleto'),
                    new StringField('Ministerio'),
                    new StringField('TelefoneFixo'),
                    new StringField('TelefoneCelular'),
                    new StringField('email'),
                    new StringField('SubSetor'),
                    new StringField('ID_CCB'),
                    new StringField('ComumCongregacao')
                )
            );
            $lookupDataset->setOrderByField('NomeCompleto', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_irmaoministerio_search', 'Id_irmaoministerio', 'NomeCompleto', null, 20);
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_Evento_search', 'id_Evento', 'Ds_Evento', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`estado`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new StringField('uf')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_Estado_search', 'id', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cidade`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true),
                    new StringField('nome'),
                    new IntegerField('estado')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CHECKIN_EVENTO_MINISTERIO_Id_Cidade_search', 'id', 'nome', null, 20);
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
        $Page = new CHECKIN_EVENTO_MINISTERIOPage("CHECKIN_EVENTO_MINISTERIO", "CHECKIN_EVENTO_MINISTERIO.php", GetCurrentUserPermissionSetForDataSource("CHECKIN_EVENTO_MINISTERIO"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("CHECKIN_EVENTO_MINISTERIO"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
