<?php
defined('MOODLE_INTERNAL') || die();

class block_eva_custom_exhibition_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        
        // Cabeçalho de configurações do bloco
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Campo para alterar os nomes das categorias presentes na barra de navegação
        $mform->addElement(
            'textarea', 
            'config_nav_categories', 
            get_string('nav_categories', 'block_eva_custom_exhibition'), 
            'wrap="virtual" rows="6" cols="50"'
        );
        $mform->setDefault('config_nav_categories', "Todos\nCursos e Eventos Inativos\nCursos\nLicitações e Contratos\nTurma I\nSuper Sapiens\nEstágio");
        $mform->setType('config_nav_categories', PARAM_TEXT);
        $mform->addHelpButton('config_nav_categories', 'nav_categories', 'block_eva_custom_exhibition');

        // Campo para controlar o limite de cursos exibidos no grid
        $mform->addElement('text', 'config_course_limit', 'Limite de Cursos Exibidos');
        $mform->setDefault('config_course_limit', 8);
        $mform->setType('config_course_limit', PARAM_INT);
    }
}
