<?php
defined('MOODLE_INTERNAL') || die();

class block_eva_course_grid_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // ====================================================================
        // 1. CONFIGURAÇÕES GERAIS DO BLOCO (TÍTULOS)
        // ====================================================================
        $mform->addElement('header', 'config_header_texts', get_string('header_texts', 'block_eva_course_grid'));

        // Campo para o Título Principal do bloco (ex: "Nossos melhores cursos")
        $mform->addElement('text', 'config_main_title', get_string('main_title', 'block_eva_course_grid'), ['size' => 50]);
        $mform->setDefault('config_main_title', 'Nossos melhores cursos');
        $mform->setType('config_main_title', PARAM_TEXT);

        // Campo para o Subtítulo
        $mform->addElement('text', 'config_subtitle', get_string('subtitle', 'block_eva_course_grid'), ['size' => 50]);
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisTexto complementar do titulo de forma reduzida modelo.');
        $mform->setType('config_subtitle', PARAM_TEXT);


        // ====================================================================
        // 2. CONFIGURAÇÃO DE ABAS E CURSOS MANUAIS
        // ====================================================================
        $mform->addElement('header', 'config_header_custom_tabs', get_string('header_custom_tabs', 'block_eva_course_grid'));

        // Instruções de uso (HTML estático)
        $instrucoes = '<div style="background-color: #f8f9fa; padding: 10px; border-left: 4px solid #007bff; margin-bottom: 15px;">
                          <p style="margin:0;"><strong>Como configurar as abas:</strong></p>
                          <small>Insira uma categoria por linha separando o nome da aba e os IDs dos cursos com uma barra vertical ( <strong>|</strong> ). Os IDs dos cursos devem ser separados por vírgula.</small><br><br>
                          <small><em>Exemplo:</em><br>
                          Todos | 4, 15, 23, 42<br>
                          Cursos e Eventos Inativos | 15, 42<br>
                          Licitações e Contratos | 4<br>
                          Super Sapiens | 23</small>
                       </div>';
        $mform->addElement('static', 'custom_tabs_instructions', '', $instrucoes);

        // Área de texto para inserção das regras
        $mform->addElement('textarea', 'config_custom_tabs', get_string('custom_tabs', 'block_eva_course_grid'), 'wrap="virtual" rows="10" cols="60" style="font-family: monospace;"');
        $mform->setType('config_custom_tabs', PARAM_RAW); // PARAM_RAW permite quebra de linhas e caracteres como | e ,
    }
}
