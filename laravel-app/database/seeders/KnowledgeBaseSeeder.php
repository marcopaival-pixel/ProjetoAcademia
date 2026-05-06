<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Str;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'ALUNO' => [
                'Geral' => [
                    [
                        'titulo' => 'Como acessar o sistema',
                        'conteudo' => '<p>Para acessar o sistema NexShape, utilize seu e-mail cadastrado e a senha enviada pelo seu instrutor ou clínica.</p><p>1. Vá para a página de login.<br>2. Insira suas credenciais.<br>3. Clique em "Entrar".</p><p>Se esqueceu sua senha, utilize o link "Esqueci minha senha" na tela de login.</p>'
                    ],
                    [
                        'titulo' => 'Como visualizar treinos',
                        'conteudo' => '<p>Seus treinos ficam disponíveis no dashboard principal assim que seu instrutor os libera.</p><p>1. Acesse o menu "Meus Treinos".<br>2. Selecione o plano de treino atual.<br>3. Você verá a lista de exercícios com vídeos e instruções.</p>'
                    ],
                    [
                        'titulo' => 'Como acompanhar evolução',
                        'conteudo' => '<p>Acompanhe seu progresso através de gráficos e fotos.</p><p>1. No menu lateral, acesse "Evolução".<br>2. Registre suas medidas e peso periodicamente.<br>3. Envie fotos de antes e depois para comparação visual.</p>'
                    ],
                    [
                        'titulo' => 'Como falar com suporte',
                        'conteudo' => '<p>Estamos prontos para ajudar!</p><p>1. Clique no ícone de mensagens ou acesse "Suporte" no menu.<br>2. Abra um novo chamado descrevendo sua dúvida.<br>3. Você receberá uma notificação assim que respondermos.</p>'
                    ],
                ]
            ],
            'CLINICA' => [
                'Gestão' => [
                    [
                        'titulo' => 'Como cadastrar pacientes',
                        'conteudo' => '<p>Adicione novos pacientes rapidamente para iniciar o acompanhamento.</p><p>1. No menu lateral, clique em "Pacientes".<br>2. Clique no botão "Novo Paciente".<br>3. Preencha os dados básicos e salve. O paciente receberá um convite por e-mail.</p>'
                    ],
                    [
                        'titulo' => 'Como criar atendimentos',
                        'conteudo' => '<p>Registre cada consulta ou sessão realizada.</p><p>1. Acesse o perfil do paciente.<br>2. Clique em "Novo Atendimento".<br>3. Descreva a evolução, anexe documentos e defina a próxima data.</p>'
                    ],
                    [
                        'titulo' => 'Como configurar agenda',
                        'conteudo' => '<p>Mantenha seus horários organizados.</p><p>1. Vá em "Configurações" > "Agenda".<br>2. Defina seus dias e horários de atendimento.<br>3. Bloqueie datas especiais se necessário.</p>'
                    ],
                    [
                        'titulo' => 'Como utilizar planos',
                        'conteudo' => '<p>Gerencie os pacotes de serviços oferecidos pela sua clínica.</p><p>1. Acesse "Financeiro" > "Planos".<br>2. Vincule pacientes aos planos contratados.<br>3. Acompanhe a validade e renovações automaticamente.</p>'
                    ],
                ]
            ],
            'ADMIN' => [
                'Administração' => [
                    [
                        'titulo' => 'Como gerenciar usuários',
                        'conteudo' => '<p>Controle o acesso de todos os perfis no sistema.</p><p>1. No painel admin, acesse "Usuários".<br>2. Você pode editar permissões, resetar senhas ou desativar contas.<br>3. Utilize os filtros para localizar usuários por tipo ou status.</p>'
                    ],
                    [
                        'titulo' => 'Como configurar planos',
                        'conteudo' => '<p>Defina as ofertas de assinatura do sistema.</p><p>1. Vá em "Configurações" > "Planos de Assinatura".<br>2. Edite valores, limites de recursos (ex: limite de alunos) e funcionalidades inclusas.</p>'
                    ],
                    [
                        'titulo' => 'Como visualizar relatórios',
                        'conteudo' => '<p>Acompanhe a saúde do negócio através de dados.</p><p>1. Acesse o menu "Relatórios".<br>2. Escolha entre relatórios financeiros, de crescimento ou de engajamento.<br>3. Exporte os dados para CSV ou PDF se necessário.</p>'
                    ],
                    [
                        'titulo' => 'Como gerenciar pagamentos',
                        'conteudo' => '<p>Monitore as transações e integrações financeiras.</p><p>1. No painel admin, vá em "Financeiro".<br>2. Verifique o status das integrações (Mercado Pago, etc).<br>3. Acompanhe logs de erro e conciliações bancárias.</p>'
                    ],
                ]
            ],
            'FINANCEIRO' => [
                'Pagamentos e Créditos' => [
                    [
                        'titulo' => 'Como funciona pagamento',
                        'conteudo' => '<p>O NexShape utiliza gateways seguros para processar sua assinatura.</p><p>1. Aceitamos cartão de crédito e PIX.<br>2. O acesso é liberado instantaneamente após a confirmação do pagamento.</p>'
                    ],
                    [
                        'titulo' => 'Como atualizar plano',
                        'conteudo' => '<p>Precisa de mais recursos? Faça um upgrade a qualquer momento.</p><p>1. Vá em "Meu Plano".<br>2. Escolha a nova categoria desejada.<br>3. A diferença de valores será calculada proporcionalmente.</p>'
                    ],
                    [
                        'titulo' => 'Como comprar créditos',
                        'conteudo' => '<p>Algumas funcionalidades de IA utilizam créditos avulsos.</p><p>1. Acesse o menu "Créditos IA".<br>2. Selecione um pacote de créditos.<br>3. Realize o pagamento e os créditos serão adicionados ao seu saldo.</p>'
                    ],
                ]
            ]
        ];

        foreach ($data as $tipo => $categories) {
            foreach ($categories as $catName => $articles) {
                $category = KnowledgeCategory::create([
                    'nome' => $catName,
                    'slug' => Str::slug($catName . '-' . $tipo),
                    'descricao' => "Artigos de $catName para $tipo",
                    'tipo_usuario' => $tipo,
                    'ativo' => true,
                ]);

                foreach ($articles as $art) {
                    KnowledgeArticle::create([
                        'titulo' => $art['titulo'],
                        'slug' => Str::slug($art['titulo'] . '-' . $tipo),
                        'conteudo' => $art['conteudo'],
                        'categoria_id' => $category->id,
                        'tipo_usuario' => $tipo,
                        'ativo' => true,
                    ]);
                }
            }
        }
    }
}
