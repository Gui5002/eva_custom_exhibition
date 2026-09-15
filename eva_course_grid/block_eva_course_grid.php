<?php
// Impede o acesso direto ao arquivo
defined('MOODLE_INTERNAL') || die();

class block_eva_course_grid extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_eva_course_grid');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $DB, $CFG, $OUTPUT;

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Resgata os textos configurados no formulário ou define os valores padrão
        $main_title = !empty($this->config->main_title) ? $this->config->main_title : 'Nossos melhores cursos';
        $subtitle = !empty($this->config->subtitle) ? $this->config->subtitle : 'Texto complementar do titulo de forma reduzida modelo.';
        
        // Resgata a configuração manual de abas/cursos
        $config_abas = !empty($this->config->custom_tabs) ? $this->config->custom_tabs : '';

        $abas_manuais = [];
        $todos_cursos_ids = [];

        // 1. PROCESSAMENTO DAS REGRAS (Texto para Array)
        if (!empty($config_abas)) {
            $linhas = explode("\n", $config_abas);
            foreach ($linhas as $index => $linha) {
                $linha = trim($linha);
                if (empty($linha)) continue;

                $partes = explode('|', $linha);
                if (count($partes) == 2) {
                    $nome_aba = trim($partes[0]);
                    $ids_string = trim($partes[1]);
                    
                    // Limpa espaços e pega apenas os IDs numéricos
                    $ids_cursos = array_filter(array_map('trim', explode(',', $ids_string)));

                    if (!empty($ids_cursos)) {
                        // Cria uma classe CSS única para a aba (slug)
                        $slug_aba = 'eva-cat-' . md5($nome_aba . $index); 
                        
                        $abas_manuais[$slug_aba] = [
                            'nome' => $nome_aba,
                            'cursos' => $ids_cursos
                        ];
                        
                        // Junta todos os IDs para a query do banco
                        $todos_cursos_ids = array_merge($todos_cursos_ids, $ids_cursos);
                    }
                }
            }
        }

        $todos_cursos_ids = array_unique($todos_cursos_ids);

        // 2. BUSCA DOS CURSOS NO BANCO DE DADOS
        $courses = [];
        if (!empty($todos_cursos_ids)) {
            // Retorna a string SQL (ex: ?,?,?) e os parâmetros
            list($in_sql, $params) = $DB->get_in_or_equal($todos_cursos_ids);
            
            $sql = "SELECT id, fullname, shortname, summary 
                    FROM {course} 
                    WHERE id $in_sql AND visible = 1";
            
            $courses = $DB->get_records_sql($sql, $params);
        }

        // ====================================================================
        // 3. RENDERIZAÇÃO DO HTML E MANUTENÇÃO DO DESIGN
        // ====================================================================
        
        $html = '<div class="eva-course-grid-module" style="text-align: center; margin-bottom: 2rem;">';
        
        // Cabeçalho
        $html .= '<h2 class="eva-main-title">' . s($main_title) . '</h2>';
        $html .= '<p class="eva-subtitle text-muted">' . s($subtitle) . '</p>';

        if (!empty($abas_manuais) && !empty($courses)) {
            
            // Renderiza o menu de Abas (Barra de Navegação)
            $html .= '<div class="eva-tabs-container" style="margin-bottom: 1.5rem;">';
            $html .= '<ul class="nav justify-content-center eva-custom-tabs" style="border-bottom: 2px solid #e0e0e0; display: inline-flex; padding-bottom: 5px;">';
            
            $is_first = true;
            foreach ($abas_manuais as $slug => $aba) {
                // A primeira aba começa ativa
                $active_class = $is_first ? 'active' : '';
                $html .= '<li class="nav-item" style="margin: 0 15px;">';
                $html .= '<a class="nav-link eva-tab-btn ' . $active_class . '" data-target="' . $slug . '" style="cursor: pointer; color: #666; padding: 5px 10px;">' . s($aba['nome']) . '</a>';
                $html .= '</li>';
                $is_first = false;
            }
            $html .= '</ul>';
            $html .= '</div>';

            // Renderiza a Grade de Cursos
            $html .= '<div class="row eva-courses-grid text-left" style="text-align: left;">';

            foreach ($courses as $curso) {
                // Descobre a quais abas este curso pertence para adicionar como classe CSS
                $classes_do_curso = [];
                foreach ($abas_manuais as $slug => $aba) {
                    if (in_array($curso->id, $aba['cursos'])) {
                        $classes_do_curso[] = $slug;
                    }
                }
                $string_classes = implode(' ', $classes_do_curso);

                // Imagem de capa do curso (Lógica nativa do Moodle)
                $course_context = context_course::instance($curso->id);
                $course_image = $OUTPUT->pix_url('u/f1')->out(); // Fallback genérico
                // Se você tiver uma função específica no seu tema para pegar a capa do curso, substitua aqui.

                // ==============================================================================
                // IMPORTANTE: Mantenha as classes internas abaixo iguais ao seu design original
                // Apenas adicionei as variáveis dinâmicas e a `$string_classes` no container.
                // ==============================================================================
                $html .= '<div class="col-md-3 mb-4 eva-course-card ' . $string_classes . '">';
                $html .= '  <div class="card h-100 shadow-sm border-0">';
                // Container da imagem (Ajuste as classes HTML conforme a sua estrutura real)
                $html .= '    <div class="card-img-top" style="height: 160px; background-color: #315b7d; background-image: url('.$course_image.'); background-size: cover; background-position: center;"></div>';
                $html .= '    <div class="card-body">';
                $html .= '      <h5 class="card-title" style="color: #0056b3; font-size: 1.1rem; font-weight: 500;">' . s($curso->fullname) . '</h5>';
                $html .= '    </div>';
                $html .= '    <div class="card-footer bg-white border-top-0 text-muted" style="font-size: 0.9rem;">';
                // Placeholder para os ícones de alunos e comentários que existem na sua imagem
                $html .= '      <i class="fa fa-user"></i> 0 &nbsp;&nbsp; <i class="fa fa-comments"></i> 0';
                $html .= '    </div>';
                $html .= '  </div>';
                $html .= '</div>';
            }

            $html .= '</div>'; // Fim row

        } else {
            // Mensagem caso não tenha nada configurado
            $html .= '<div class="alert alert-info">Nenhum curso configurado ou encontrado. Configure as abas nas configurações do bloco.</div>';
        }

        // Botão Footer "Ver todos os Cursos"
        $html .= '<div class="eva-footer-action mt-4">';
        $html .= '  <a href="' . $CFG->wwwroot . '/course/" class="btn btn-outline-secondary" style="border-radius: 25px; padding: 5px 25px;">Ver todos os Cursos</a>';
        $html .= '</div>';

        $html .= '</div>'; // Fim do modulo

        // ====================================================================
        // 4. JAVASCRIPT PARA FILTRAGEM (Visualização Dinâmica)
        // ====================================================================
        // Este JS oculta/exibe os cards baseado na aba clicada
        
        $html .= "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var abas = document.querySelectorAll('.eva-tab-btn');
                var cards = document.querySelectorAll('.eva-course-card');

                abas.forEach(function(aba) {
                    aba.addEventListener('click', function() {
                        // Remove o estilo ativo de todas as abas
                        abas.forEach(function(btn) {
                            btn.classList.remove('active');
                            // Reseta o estilo visual (ajuste de acordo com o seu CSS)
                            btn.style.color = '#666';
                            btn.style.fontWeight = 'normal';
                            btn.style.borderBottom = 'none';
                        });

                        // Adiciona estilo ativo na aba clicada
                        this.classList.add('active');
                        this.style.color = '#315b7d'; // Azul do layout
                        this.style.fontWeight = 'bold';
                        this.style.borderBottom = '2px solid #e0e0e0'; 

                        var targetSlug = this.getAttribute('data-target');

                        // Mostra ou oculta os cards baseados na classe alvo
                        cards.forEach(function(card) {
                            if (card.classList.contains(targetSlug)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                });

                // Simula um clique na primeira aba para carregar o filtro inicial
                if (abas.length > 0) {
                    abas[0].click();
                }
            });
        </script>
        ";

        $this->content->text = $html;

        return $this->content;
    }
}
