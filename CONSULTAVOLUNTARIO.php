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
    
    
    
    class CONSULTAVOLUNTARIOPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Consulta Voluntários');
            $this->SetMenuLabel('Consulta Voluntários');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $selectQuery = 'select concat(replace(replace(v.id_voluntario,\'-\',\'\'),\'.\',\'\'),\' \',v.nm_voluntario) as descricao,v.id_voluntario id_voluntario,v.nm_voluntario nm_voluntario,
            c.ds_subsetor,v.id_ccb, v.id_aux,
            v.id_funcao1, v.id_funcao2, v.id_funcao3,v.dt_nasc_voluntario,v.tel1_voluntario,v.tel2_voluntario,v.tel3_voluntario,
            v.foto_voluntario,v.thumb_voluntario,v.DT_ALTERACAO,v.NM_COMUM_CCB,v.CD_RG_VOLUNTARIO
            from cadvoluntarios v
            join cadcongregacoes c on v.id_ccb = c.id_ccb';
            $insertQuery = array('insert into cadvoluntarios
            (id_voluntario,nm_voluntario,id_ccb,
            id_funcao1, id_funcao2, id_funcao3,dt_nasc_voluntario,tel1_voluntario,tel2_voluntario,tel3_voluntario,
            foto_voluntario,thumb_voluntario,dt_alteracao,NM_COMUM_CCB,CD_RG_VOLUNTARIO) values
            (:id_voluntario,UPPER(:nm_voluntario),:id_ccb,
            :id_funcao1, :id_funcao2, :id_funcao3,:dt_nasc_voluntario,:tel1_voluntario,:tel2_voluntario,:tel3_voluntario,
            :foto_voluntario,:thumb_voluntario,now(),UPPER(:NM_COMUM_CCB),UPPER(:CD_RG_VOLUNTARIO))');
            $updateQuery = array('update cadvoluntarios
            set id_voluntario = :id_voluntario,
            nm_voluntario = UPPER(:nm_voluntario),
            id_ccb = :id_ccb,
            id_funcao1 = :id_funcao1, 
            id_funcao2 = :id_funcao2, 
            id_funcao3 = :id_funcao3,
            dt_nasc_voluntario = :dt_nasc_voluntario,
            tel1_voluntario = :tel1_voluntario,
            tel2_voluntario = :tel2_voluntario,
            tel3_voluntario = :tel3_voluntario,
            foto_voluntario = :foto_voluntario,
            thumb_voluntario = :thumb_voluntario,
            dt_alteracao = now(),
            NM_COMUM_CCB = UPPER(:NM_COMUM_CCB),
            CD_RG_VOLUNTARIO = UPPER(:CD_RG_VOLUNTARIO)
            where id_aux = :old_id_aux', 
            'DELETE FROM convocacoeseventos WHERE id_aux = :old_id_aux AND Id_Evento = \'1\'', 
            'INSERT INTO convocacoeseventos
            (Id_Evento,Id_Voluntario,St_VoluntarioCompareceu,Dt_Hr_Chegada,Dt_Hr_Saida,ID_AUX) values
            (\'1\',:Id_Voluntario,\'SIM\',NOW(),NOW(),:id_aux)');
            $deleteQuery = array('DELETE FROM cadvoluntarios WHERE id_aux = :old_id_aux');
            $this->dataset = new QueryDataset(
              MySqlIConnectionFactory::getInstance(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'CONSULTAVOLUNTARIO');
            $this->dataset->addFields(
                array(
                    new StringField('descricao'),
                    new StringField('id_voluntario'),
                    new StringField('nm_voluntario'),
                    new StringField('ds_subsetor'),
                    new StringField('id_ccb'),
                    new IntegerField('id_aux', true, true, true),
                    new IntegerField('id_funcao1'),
                    new IntegerField('id_funcao2'),
                    new IntegerField('id_funcao3'),
                    new StringField('dt_nasc_voluntario'),
                    new StringField('tel1_voluntario'),
                    new StringField('tel2_voluntario'),
                    new StringField('tel3_voluntario'),
                    new StringField('foto_voluntario'),
                    new StringField('thumb_voluntario'),
                    new DateTimeField('DT_ALTERACAO'),
                    new StringField('NM_COMUM_CCB'),
                    new StringField('CD_RG_VOLUNTARIO')
                )
            );
            $this->dataset->AddLookupField('id_ccb', 'cadcongregacoes', new StringField('Id_CCB'), new StringField('Ds_CCB', false, false, false, false, 'id_ccb_Ds_CCB', 'id_ccb_Ds_CCB_cadcongregacoes'), 'id_ccb_Ds_CCB_cadcongregacoes');
            $this->dataset->AddLookupField('id_funcao1', 'funcoes', new IntegerField('Id_Funcao'), new StringField('Ds_Funcao', false, false, false, false, 'id_funcao1_Ds_Funcao', 'id_funcao1_Ds_Funcao_funcoes'), 'id_funcao1_Ds_Funcao_funcoes');
            $this->dataset->AddLookupField('id_funcao2', 'funcoes', new IntegerField('Id_Funcao'), new StringField('Ds_Funcao', false, false, false, false, 'id_funcao2_Ds_Funcao', 'id_funcao2_Ds_Funcao_funcoes'), 'id_funcao2_Ds_Funcao_funcoes');
            $this->dataset->AddLookupField('id_funcao3', 'funcoes', new IntegerField('Id_Funcao'), new StringField('Ds_Funcao', false, false, false, false, 'id_funcao3_Ds_Funcao', 'id_funcao3_Ds_Funcao_funcoes'), 'id_funcao3_Ds_Funcao_funcoes');
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
                new FilterColumn($this->dataset, 'descricao', 'descricao', 'Descrição'),
                new FilterColumn($this->dataset, 'id_voluntario', 'id_voluntario', 'CPF'),
                new FilterColumn($this->dataset, 'CD_RG_VOLUNTARIO', 'CD_RG_VOLUNTARIO', 'RG'),
                new FilterColumn($this->dataset, 'nm_voluntario', 'nm_voluntario', 'Nome'),
                new FilterColumn($this->dataset, 'ds_subsetor', 'ds_subsetor', 'SubSetor'),
                new FilterColumn($this->dataset, 'id_ccb', 'id_ccb_Ds_CCB', 'CCB'),
                new FilterColumn($this->dataset, 'NM_COMUM_CCB', 'NM_COMUM_CCB', 'Comum CCB'),
                new FilterColumn($this->dataset, 'id_aux', 'id_aux', 'Id Aux'),
                new FilterColumn($this->dataset, 'id_funcao1', 'id_funcao1_Ds_Funcao', 'Função Principal'),
                new FilterColumn($this->dataset, 'id_funcao2', 'id_funcao2_Ds_Funcao', 'Função Complementar'),
                new FilterColumn($this->dataset, 'id_funcao3', 'id_funcao3_Ds_Funcao', 'Função Adicional'),
                new FilterColumn($this->dataset, 'dt_nasc_voluntario', 'dt_nasc_voluntario', 'Data Nascimento'),
                new FilterColumn($this->dataset, 'tel1_voluntario', 'tel1_voluntario', 'Telefone Fixo'),
                new FilterColumn($this->dataset, 'tel2_voluntario', 'tel2_voluntario', 'Telefone Móvel'),
                new FilterColumn($this->dataset, 'tel3_voluntario', 'tel3_voluntario', 'Telefone Adicional'),
                new FilterColumn($this->dataset, 'foto_voluntario', 'foto_voluntario', 'Foto Voluntario'),
                new FilterColumn($this->dataset, 'thumb_voluntario', 'thumb_voluntario', 'Foto'),
                new FilterColumn($this->dataset, 'DT_ALTERACAO', 'DT_ALTERACAO', 'Data Alteração')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['descricao'])
                ->addColumn($columns['id_voluntario'])
                ->addColumn($columns['CD_RG_VOLUNTARIO'])
                ->addColumn($columns['nm_voluntario'])
                ->addColumn($columns['ds_subsetor'])
                ->addColumn($columns['id_ccb'])
                ->addColumn($columns['NM_COMUM_CCB'])
                ->addColumn($columns['id_aux'])
                ->addColumn($columns['id_funcao1'])
                ->addColumn($columns['id_funcao2'])
                ->addColumn($columns['id_funcao3'])
                ->addColumn($columns['dt_nasc_voluntario'])
                ->addColumn($columns['tel1_voluntario'])
                ->addColumn($columns['tel2_voluntario'])
                ->addColumn($columns['tel3_voluntario'])
                ->addColumn($columns['foto_voluntario'])
                ->addColumn($columns['thumb_voluntario'])
                ->addColumn($columns['DT_ALTERACAO']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('id_ccb')
                ->setOptionsFor('id_funcao1')
                ->setOptionsFor('id_funcao2')
                ->setOptionsFor('id_funcao3')
                ->setOptionsFor('DT_ALTERACAO');
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
            
            $main_editor = new MaskedEdit('id_voluntario_edit', '999.999.999-99');
            
            $text_editor = new TextEdit('id_voluntario');
            
            $filterBuilder->addColumn(
                $columns['id_voluntario'],
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
            
            $main_editor = new TextEdit('cd_rg_voluntario_edit');
            
            $filterBuilder->addColumn(
                $columns['CD_RG_VOLUNTARIO'],
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
            
            $main_editor = new TextEdit('nm_voluntario_edit');
            
            $filterBuilder->addColumn(
                $columns['nm_voluntario'],
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
            
            $main_editor = new TextEdit('ds_subsetor_edit');
            
            $filterBuilder->addColumn(
                $columns['ds_subsetor'],
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
            $main_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_ccb_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('id_ccb', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_ccb_search');
            
            $text_editor = new TextEdit('id_ccb');
            
            $filterBuilder->addColumn(
                $columns['id_ccb'],
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
            
            $main_editor = new TextEdit('nm_comum_ccb_edit');
            
            $filterBuilder->addColumn(
                $columns['NM_COMUM_CCB'],
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
            
            $main_editor = new SpinEdit('id_aux_edit');
            
            $filterBuilder->addColumn(
                $columns['id_aux'],
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
            
            $main_editor = new DynamicCombobox('id_funcao1_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_funcao1_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('id_funcao1', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_funcao1_search');
            
            $filterBuilder->addColumn(
                $columns['id_funcao1'],
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
            
            $main_editor = new DynamicCombobox('id_funcao2_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_funcao2_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('id_funcao2', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_funcao2_search');
            
            $filterBuilder->addColumn(
                $columns['id_funcao2'],
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
            
            $main_editor = new DynamicCombobox('id_funcao3_edit', $this->CreateLinkBuilder());
            $main_editor->setAllowClear(true);
            $main_editor->setMinimumInputLength(0);
            $main_editor->SetAllowNullValue(false);
            $main_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_funcao3_search');
            
            $multi_value_select_editor = new RemoteMultiValueSelect('id_funcao3', $this->CreateLinkBuilder());
            $multi_value_select_editor->SetHandlerName('filter_builder_CONSULTAVOLUNTARIO_id_funcao3_search');
            
            $filterBuilder->addColumn(
                $columns['id_funcao3'],
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
            
            $main_editor = new MaskedEdit('dt_nasc_voluntario_edit', '99/99/9999');
            
            $text_editor = new TextEdit('dt_nasc_voluntario');
            
            $filterBuilder->addColumn(
                $columns['dt_nasc_voluntario'],
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
            
            $main_editor = new MaskedEdit('tel1_voluntario_edit', '(99) 9999-9999');
            
            $text_editor = new TextEdit('tel1_voluntario');
            
            $filterBuilder->addColumn(
                $columns['tel1_voluntario'],
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
            
            $main_editor = new MaskedEdit('tel2_voluntario_edit', '(99) 9 9999-9999');
            
            $text_editor = new TextEdit('tel2_voluntario');
            
            $filterBuilder->addColumn(
                $columns['tel2_voluntario'],
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
            
            $main_editor = new MaskedEdit('tel3_voluntario_edit', '(99) 9 9999-9999');
            
            $text_editor = new TextEdit('tel3_voluntario');
            
            $filterBuilder->addColumn(
                $columns['tel3_voluntario'],
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
            
            $main_editor = new TextEdit('foto_voluntario');
            
            $filterBuilder->addColumn(
                $columns['foto_voluntario'],
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
            
            $main_editor = new TextEdit('thumb_voluntario_edit');
            
            $filterBuilder->addColumn(
                $columns['thumb_voluntario'],
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
            
            $main_editor = new DateTimeEdit('dt_alteracao_edit', false, 'Y-m-d H:i:s');
            
            $filterBuilder->addColumn(
                $columns['DT_ALTERACAO'],
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
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for id_voluntario field
            //
            $column = new TextViewColumn('id_voluntario', 'id_voluntario', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for CD_RG_VOLUNTARIO field
            //
            $column = new TextViewColumn('CD_RG_VOLUNTARIO', 'CD_RG_VOLUNTARIO', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for nm_voluntario field
            //
            $column = new TextViewColumn('nm_voluntario', 'nm_voluntario', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for ds_subsetor field
            //
            $column = new TextViewColumn('ds_subsetor', 'ds_subsetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('id_ccb', 'id_ccb_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for NM_COMUM_CCB field
            //
            $column = new TextViewColumn('NM_COMUM_CCB', 'NM_COMUM_CCB', 'Comum CCB', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao1', 'id_funcao1_Ds_Funcao', 'Função Principal', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao2', 'id_funcao2_Ds_Funcao', 'Função Complementar', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao3', 'id_funcao3_Ds_Funcao', 'Função Adicional', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for dt_nasc_voluntario field
            //
            $column = new TextViewColumn('dt_nasc_voluntario', 'dt_nasc_voluntario', 'Data Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for tel1_voluntario field
            //
            $column = new TextViewColumn('tel1_voluntario', 'tel1_voluntario', 'Telefone Fixo', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for tel2_voluntario field
            //
            $column = new TextViewColumn('tel2_voluntario', 'tel2_voluntario', 'Telefone Móvel', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for tel3_voluntario field
            //
            $column = new TextViewColumn('tel3_voluntario', 'tel3_voluntario', 'Telefone Adicional', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for thumb_voluntario field
            //
            $column = new ExternalImageViewColumn('thumb_voluntario', 'thumb_voluntario', 'Foto', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for DT_ALTERACAO field
            //
            $column = new DateTimeViewColumn('DT_ALTERACAO', 'DT_ALTERACAO', 'Data Alteração', $this->dataset);
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
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for id_voluntario field
            //
            $column = new TextViewColumn('id_voluntario', 'id_voluntario', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for CD_RG_VOLUNTARIO field
            //
            $column = new TextViewColumn('CD_RG_VOLUNTARIO', 'CD_RG_VOLUNTARIO', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for nm_voluntario field
            //
            $column = new TextViewColumn('nm_voluntario', 'nm_voluntario', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for ds_subsetor field
            //
            $column = new TextViewColumn('ds_subsetor', 'ds_subsetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('id_ccb', 'id_ccb_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for NM_COMUM_CCB field
            //
            $column = new TextViewColumn('NM_COMUM_CCB', 'NM_COMUM_CCB', 'Comum CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao1', 'id_funcao1_Ds_Funcao', 'Função Principal', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao2', 'id_funcao2_Ds_Funcao', 'Função Complementar', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao3', 'id_funcao3_Ds_Funcao', 'Função Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for dt_nasc_voluntario field
            //
            $column = new TextViewColumn('dt_nasc_voluntario', 'dt_nasc_voluntario', 'Data Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for tel1_voluntario field
            //
            $column = new TextViewColumn('tel1_voluntario', 'tel1_voluntario', 'Telefone Fixo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for tel2_voluntario field
            //
            $column = new TextViewColumn('tel2_voluntario', 'tel2_voluntario', 'Telefone Móvel', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for tel3_voluntario field
            //
            $column = new TextViewColumn('tel3_voluntario', 'tel3_voluntario', 'Telefone Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for foto_voluntario field
            //
            $column = new ExternalImageViewColumn('foto_voluntario', 'foto_voluntario', 'Foto Voluntario', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for DT_ALTERACAO field
            //
            $column = new DateTimeViewColumn('DT_ALTERACAO', 'DT_ALTERACAO', 'Data Alteração', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for id_voluntario field
            //
            $editor = new MaskedEdit('id_voluntario_edit', '999.999.999-99');
            $editColumn = new CustomEditColumn('CPF', 'id_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for CD_RG_VOLUNTARIO field
            //
            $editor = new TextEdit('cd_rg_voluntario_edit');
            $editColumn = new CustomEditColumn('RG', 'CD_RG_VOLUNTARIO', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for nm_voluntario field
            //
            $editor = new TextEdit('nm_voluntario_edit');
            $editColumn = new CustomEditColumn('Nome', 'nm_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_ccb field
            //
            $editor = new DynamicCombobox('id_ccb_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
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
            $editColumn = new DynamicLookupEditColumn('CCB', 'id_ccb', 'id_ccb_Ds_CCB', 'edit_CONSULTAVOLUNTARIO_id_ccb_search', $editor, $this->dataset, $lookupDataset, 'Id_CCB', 'Ds_CCB', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for NM_COMUM_CCB field
            //
            $editor = new TextEdit('nm_comum_ccb_edit');
            $editColumn = new CustomEditColumn('Comum CCB', 'NM_COMUM_CCB', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_funcao1 field
            //
            $editor = new DynamicCombobox('id_funcao1_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Principal', 'id_funcao1', 'id_funcao1_Ds_Funcao', 'edit_CONSULTAVOLUNTARIO_id_funcao1_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_funcao2 field
            //
            $editor = new DynamicCombobox('id_funcao2_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Complementar', 'id_funcao2', 'id_funcao2_Ds_Funcao', 'edit_CONSULTAVOLUNTARIO_id_funcao2_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for id_funcao3 field
            //
            $editor = new DynamicCombobox('id_funcao3_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Adicional', 'id_funcao3', 'id_funcao3_Ds_Funcao', 'edit_CONSULTAVOLUNTARIO_id_funcao3_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for dt_nasc_voluntario field
            //
            $editor = new MaskedEdit('dt_nasc_voluntario_edit', '99/99/9999');
            $editColumn = new CustomEditColumn('Data Nascimento', 'dt_nasc_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for tel1_voluntario field
            //
            $editor = new MaskedEdit('tel1_voluntario_edit', '(99) 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Fixo', 'tel1_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for tel2_voluntario field
            //
            $editor = new MaskedEdit('tel2_voluntario_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Móvel', 'tel2_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for tel3_voluntario field
            //
            $editor = new MaskedEdit('tel3_voluntario_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Adicional', 'tel3_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for foto_voluntario field
            //
            $editor = new ImageUploader('foto_voluntario_edit');
            $editor->SetShowImage(true);
            $editor->setAcceptableFileTypes('image/*');
            $editColumn = new UploadFileToFolderColumn('Foto Voluntario', 'foto_voluntario', $editor, $this->dataset, false, false, 'fotovoluntario/', '%random%.%original_file_extension%', $this->OnFileUpload, false);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetGenerationImageThumbnails(
                'thumb_voluntario',
                'fotovoluntario/',
                Delegate::CreateFromMethod($this, 'foto_voluntario_Thumbnail_GenerateFileName_edit'),
                new ImageFitByHeightResizeFilter(30),
                false
            );
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for id_voluntario field
            //
            $editor = new MaskedEdit('id_voluntario_edit', '999.999.999-99');
            $editColumn = new CustomEditColumn('CPF', 'id_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for CD_RG_VOLUNTARIO field
            //
            $editor = new TextEdit('cd_rg_voluntario_edit');
            $editColumn = new CustomEditColumn('RG', 'CD_RG_VOLUNTARIO', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for nm_voluntario field
            //
            $editor = new TextEdit('nm_voluntario_edit');
            $editColumn = new CustomEditColumn('Nome', 'nm_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for id_ccb field
            //
            $editor = new DynamicCombobox('id_ccb_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
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
            $editColumn = new DynamicLookupEditColumn('CCB', 'id_ccb', 'id_ccb_Ds_CCB', 'multi_edit_CONSULTAVOLUNTARIO_id_ccb_search', $editor, $this->dataset, $lookupDataset, 'Id_CCB', 'Ds_CCB', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for NM_COMUM_CCB field
            //
            $editor = new TextEdit('nm_comum_ccb_edit');
            $editColumn = new CustomEditColumn('Comum CCB', 'NM_COMUM_CCB', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for id_funcao1 field
            //
            $editor = new DynamicCombobox('id_funcao1_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Principal', 'id_funcao1', 'id_funcao1_Ds_Funcao', 'multi_edit_CONSULTAVOLUNTARIO_id_funcao1_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for id_funcao2 field
            //
            $editor = new DynamicCombobox('id_funcao2_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Complementar', 'id_funcao2', 'id_funcao2_Ds_Funcao', 'multi_edit_CONSULTAVOLUNTARIO_id_funcao2_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for id_funcao3 field
            //
            $editor = new DynamicCombobox('id_funcao3_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Adicional', 'id_funcao3', 'id_funcao3_Ds_Funcao', 'multi_edit_CONSULTAVOLUNTARIO_id_funcao3_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for dt_nasc_voluntario field
            //
            $editor = new MaskedEdit('dt_nasc_voluntario_edit', '99/99/9999');
            $editColumn = new CustomEditColumn('Data Nascimento', 'dt_nasc_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for tel1_voluntario field
            //
            $editor = new MaskedEdit('tel1_voluntario_edit', '(99) 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Fixo', 'tel1_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for tel2_voluntario field
            //
            $editor = new MaskedEdit('tel2_voluntario_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Móvel', 'tel2_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for tel3_voluntario field
            //
            $editor = new MaskedEdit('tel3_voluntario_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Adicional', 'tel3_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for foto_voluntario field
            //
            $editor = new ImageUploader('foto_voluntario_edit');
            $editor->SetShowImage(true);
            $editor->setAcceptableFileTypes('image/*');
            $editColumn = new UploadFileToFolderColumn('Foto Voluntario', 'foto_voluntario', $editor, $this->dataset, false, false, 'fotovoluntario/', '%random%.%original_file_extension%', $this->OnFileUpload, false);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetGenerationImageThumbnails(
                'thumb_voluntario',
                'fotovoluntario/',
                Delegate::CreateFromMethod($this, 'foto_voluntario_Thumbnail_GenerateFileName_multi_edit'),
                new ImageFitByHeightResizeFilter(30),
                false
            );
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for id_voluntario field
            //
            $editor = new MaskedEdit('id_voluntario_edit', '999.999.999-99');
            $editColumn = new CustomEditColumn('CPF', 'id_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for CD_RG_VOLUNTARIO field
            //
            $editor = new TextEdit('cd_rg_voluntario_edit');
            $editColumn = new CustomEditColumn('RG', 'CD_RG_VOLUNTARIO', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for nm_voluntario field
            //
            $editor = new TextEdit('nm_voluntario_edit');
            $editColumn = new CustomEditColumn('Nome', 'nm_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_ccb field
            //
            $editor = new DynamicCombobox('id_ccb_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
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
            $editColumn = new DynamicLookupEditColumn('CCB', 'id_ccb', 'id_ccb_Ds_CCB', 'insert_CONSULTAVOLUNTARIO_id_ccb_search', $editor, $this->dataset, $lookupDataset, 'Id_CCB', 'Ds_CCB', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for NM_COMUM_CCB field
            //
            $editor = new TextEdit('nm_comum_ccb_edit');
            $editColumn = new CustomEditColumn('Comum CCB', 'NM_COMUM_CCB', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_funcao1 field
            //
            $editor = new DynamicCombobox('id_funcao1_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Principal', 'id_funcao1', 'id_funcao1_Ds_Funcao', 'insert_CONSULTAVOLUNTARIO_id_funcao1_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_funcao2 field
            //
            $editor = new DynamicCombobox('id_funcao2_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Complementar', 'id_funcao2', 'id_funcao2_Ds_Funcao', 'insert_CONSULTAVOLUNTARIO_id_funcao2_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for id_funcao3 field
            //
            $editor = new DynamicCombobox('id_funcao3_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Função Adicional', 'id_funcao3', 'id_funcao3_Ds_Funcao', 'insert_CONSULTAVOLUNTARIO_id_funcao3_search', $editor, $this->dataset, $lookupDataset, 'Id_Funcao', 'Ds_Funcao', '');
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for dt_nasc_voluntario field
            //
            $editor = new MaskedEdit('dt_nasc_voluntario_edit', '99/99/9999');
            $editColumn = new CustomEditColumn('Data Nascimento', 'dt_nasc_voluntario', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for tel1_voluntario field
            //
            $editor = new MaskedEdit('tel1_voluntario_edit', '(99) 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Fixo', 'tel1_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for tel2_voluntario field
            //
            $editor = new MaskedEdit('tel2_voluntario_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Móvel', 'tel2_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for tel3_voluntario field
            //
            $editor = new MaskedEdit('tel3_voluntario_edit', '(99) 9 9999-9999');
            $editColumn = new CustomEditColumn('Telefone Adicional', 'tel3_voluntario', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for foto_voluntario field
            //
            $editor = new ImageUploader('foto_voluntario_edit');
            $editor->SetShowImage(true);
            $editor->setAcceptableFileTypes('image/*');
            $editColumn = new UploadFileToFolderColumn('Foto Voluntario', 'foto_voluntario', $editor, $this->dataset, false, false, 'fotovoluntario/', '%random%.%original_file_extension%', $this->OnFileUpload, false);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetGenerationImageThumbnails(
                'thumb_voluntario',
                'fotovoluntario/',
                Delegate::CreateFromMethod($this, 'foto_voluntario_Thumbnail_GenerateFileName_insert'),
                new ImageFitByHeightResizeFilter(30),
                false
            );
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
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for id_voluntario field
            //
            $column = new TextViewColumn('id_voluntario', 'id_voluntario', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for CD_RG_VOLUNTARIO field
            //
            $column = new TextViewColumn('CD_RG_VOLUNTARIO', 'CD_RG_VOLUNTARIO', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for nm_voluntario field
            //
            $column = new TextViewColumn('nm_voluntario', 'nm_voluntario', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for ds_subsetor field
            //
            $column = new TextViewColumn('ds_subsetor', 'ds_subsetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('id_ccb', 'id_ccb_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for NM_COMUM_CCB field
            //
            $column = new TextViewColumn('NM_COMUM_CCB', 'NM_COMUM_CCB', 'Comum CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for id_aux field
            //
            $column = new NumberViewColumn('id_aux', 'id_aux', 'Id Aux', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao1', 'id_funcao1_Ds_Funcao', 'Função Principal', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao2', 'id_funcao2_Ds_Funcao', 'Função Complementar', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao3', 'id_funcao3_Ds_Funcao', 'Função Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for dt_nasc_voluntario field
            //
            $column = new TextViewColumn('dt_nasc_voluntario', 'dt_nasc_voluntario', 'Data Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for tel1_voluntario field
            //
            $column = new TextViewColumn('tel1_voluntario', 'tel1_voluntario', 'Telefone Fixo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for tel2_voluntario field
            //
            $column = new TextViewColumn('tel2_voluntario', 'tel2_voluntario', 'Telefone Móvel', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for tel3_voluntario field
            //
            $column = new TextViewColumn('tel3_voluntario', 'tel3_voluntario', 'Telefone Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for foto_voluntario field
            //
            $column = new ExternalImageViewColumn('foto_voluntario', 'foto_voluntario', 'Foto Voluntario', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for thumb_voluntario field
            //
            $column = new ExternalImageViewColumn('thumb_voluntario', 'thumb_voluntario', 'Foto', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for DT_ALTERACAO field
            //
            $column = new DateTimeViewColumn('DT_ALTERACAO', 'DT_ALTERACAO', 'Data Alteração', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for id_voluntario field
            //
            $column = new TextViewColumn('id_voluntario', 'id_voluntario', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for CD_RG_VOLUNTARIO field
            //
            $column = new TextViewColumn('CD_RG_VOLUNTARIO', 'CD_RG_VOLUNTARIO', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for nm_voluntario field
            //
            $column = new TextViewColumn('nm_voluntario', 'nm_voluntario', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for ds_subsetor field
            //
            $column = new TextViewColumn('ds_subsetor', 'ds_subsetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('id_ccb', 'id_ccb_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for NM_COMUM_CCB field
            //
            $column = new TextViewColumn('NM_COMUM_CCB', 'NM_COMUM_CCB', 'Comum CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for id_aux field
            //
            $column = new NumberViewColumn('id_aux', 'id_aux', 'Id Aux', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator(',');
            $column->setDecimalSeparator('');
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao1', 'id_funcao1_Ds_Funcao', 'Função Principal', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao2', 'id_funcao2_Ds_Funcao', 'Função Complementar', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao3', 'id_funcao3_Ds_Funcao', 'Função Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for dt_nasc_voluntario field
            //
            $column = new TextViewColumn('dt_nasc_voluntario', 'dt_nasc_voluntario', 'Data Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for tel1_voluntario field
            //
            $column = new TextViewColumn('tel1_voluntario', 'tel1_voluntario', 'Telefone Fixo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for tel2_voluntario field
            //
            $column = new TextViewColumn('tel2_voluntario', 'tel2_voluntario', 'Telefone Móvel', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for tel3_voluntario field
            //
            $column = new TextViewColumn('tel3_voluntario', 'tel3_voluntario', 'Telefone Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for foto_voluntario field
            //
            $column = new ExternalImageViewColumn('foto_voluntario', 'foto_voluntario', 'Foto Voluntario', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for thumb_voluntario field
            //
            $column = new ExternalImageViewColumn('thumb_voluntario', 'thumb_voluntario', 'Foto', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for DT_ALTERACAO field
            //
            $column = new DateTimeViewColumn('DT_ALTERACAO', 'DT_ALTERACAO', 'Data Alteração', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for id_voluntario field
            //
            $column = new TextViewColumn('id_voluntario', 'id_voluntario', 'CPF', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for CD_RG_VOLUNTARIO field
            //
            $column = new TextViewColumn('CD_RG_VOLUNTARIO', 'CD_RG_VOLUNTARIO', 'RG', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for nm_voluntario field
            //
            $column = new TextViewColumn('nm_voluntario', 'nm_voluntario', 'Nome', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for ds_subsetor field
            //
            $column = new TextViewColumn('ds_subsetor', 'ds_subsetor', 'SubSetor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_CCB field
            //
            $column = new TextViewColumn('id_ccb', 'id_ccb_Ds_CCB', 'CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for NM_COMUM_CCB field
            //
            $column = new TextViewColumn('NM_COMUM_CCB', 'NM_COMUM_CCB', 'Comum CCB', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao1', 'id_funcao1_Ds_Funcao', 'Função Principal', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao2', 'id_funcao2_Ds_Funcao', 'Função Complementar', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for Ds_Funcao field
            //
            $column = new TextViewColumn('id_funcao3', 'id_funcao3_Ds_Funcao', 'Função Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for dt_nasc_voluntario field
            //
            $column = new TextViewColumn('dt_nasc_voluntario', 'dt_nasc_voluntario', 'Data Nascimento', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for tel1_voluntario field
            //
            $column = new TextViewColumn('tel1_voluntario', 'tel1_voluntario', 'Telefone Fixo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for tel2_voluntario field
            //
            $column = new TextViewColumn('tel2_voluntario', 'tel2_voluntario', 'Telefone Móvel', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for tel3_voluntario field
            //
            $column = new TextViewColumn('tel3_voluntario', 'tel3_voluntario', 'Telefone Adicional', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for foto_voluntario field
            //
            $column = new ExternalImageViewColumn('foto_voluntario', 'foto_voluntario', 'Foto Voluntario', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for thumb_voluntario field
            //
            $column = new ExternalImageViewColumn('thumb_voluntario', 'thumb_voluntario', 'Foto', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for DT_ALTERACAO field
            //
            $column = new DateTimeViewColumn('DT_ALTERACAO', 'DT_ALTERACAO', 'Data Alteração', $this->dataset);
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
    
        public function foto_voluntario_Thumbnail_GenerateFileName_insert(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), 'fotovoluntario/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = FileUtils::AppendFileExtension(rand(), $original_file_extension);
        $filepath = Path::Combine($targetFolder, $filename);
        
        while (file_exists($filepath))
        {
            $filename = FileUtils::AppendFileExtension(rand(), $original_file_extension);
            $filepath = Path::Combine($targetFolder, $filename);
        }
        
        $handled = true;
        }
        
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        public function foto_voluntario_Thumbnail_GenerateFileName_edit(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), 'fotovoluntario/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = FileUtils::AppendFileExtension(rand(), $original_file_extension);
        $filepath = Path::Combine($targetFolder, $filename);
        
        while (file_exists($filepath))
        {
            $filename = FileUtils::AppendFileExtension(rand(), $original_file_extension);
            $filepath = Path::Combine($targetFolder, $filename);
        }
        
        $handled = true;
        }
        public function foto_voluntario_Thumbnail_GenerateFileName_multi_edit(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), 'fotovoluntario/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = FileUtils::AppendFileExtension(rand(), $original_file_extension);
        $filepath = Path::Combine($targetFolder, $filename);
        
        while (file_exists($filepath))
        {
            $filename = FileUtils::AppendFileExtension(rand(), $original_file_extension);
            $filepath = Path::Combine($targetFolder, $filename);
        }
        
        $handled = true;
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CONSULTAVOLUNTARIO_id_ccb_search', 'Id_CCB', 'Ds_CCB', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CONSULTAVOLUNTARIO_id_funcao1_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CONSULTAVOLUNTARIO_id_funcao2_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_CONSULTAVOLUNTARIO_id_funcao3_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CONSULTAVOLUNTARIO_id_ccb_search', 'Id_CCB', 'Ds_CCB', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CONSULTAVOLUNTARIO_id_funcao1_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CONSULTAVOLUNTARIO_id_funcao2_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'filter_builder_CONSULTAVOLUNTARIO_id_funcao3_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CONSULTAVOLUNTARIO_id_ccb_search', 'Id_CCB', 'Ds_CCB', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CONSULTAVOLUNTARIO_id_funcao1_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CONSULTAVOLUNTARIO_id_funcao2_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_CONSULTAVOLUNTARIO_id_funcao3_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
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
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CONSULTAVOLUNTARIO_id_ccb_search', 'Id_CCB', 'Ds_CCB', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CONSULTAVOLUNTARIO_id_funcao1_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CONSULTAVOLUNTARIO_id_funcao2_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MySqlIConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcoes`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('Id_Funcao', true, true, true),
                    new StringField('Ds_Funcao'),
                    new IntegerField('id_tipofuncao')
                )
            );
            $lookupDataset->setOrderByField('Ds_Funcao', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_CONSULTAVOLUNTARIO_id_funcao3_search', 'Id_Funcao', 'Ds_Funcao', null, 20);
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
        $Page = new CONSULTAVOLUNTARIOPage("CONSULTAVOLUNTARIO", "CONSULTAVOLUNTARIO.php", GetCurrentUserPermissionSetForDataSource("CONSULTAVOLUNTARIO"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("CONSULTAVOLUNTARIO"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
