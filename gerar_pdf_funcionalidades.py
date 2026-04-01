"""Gera PDF com lista de funcionalidades do app de alimentação e exercícios."""

from pathlib import Path

from fpdf import FPDF
from fpdf.enums import XPos, YPos

PAGE_TEXT_W = 190  # A4 com margens 10 mm


class PDF(FPDF):
    def footer(self):
        self.set_y(-15)
        self.set_font("Helvetica", "", 8)
        self.set_text_color(100, 100, 100)
        self.cell(0, 10, f"Página {self.page_no()}/{{nb}}", align="C")


def add_section_title(pdf: PDF, title: str):
    pdf.ln(4)
    pdf.set_font("Helvetica", "B", 14)
    pdf.set_text_color(30, 30, 30)
    pdf.multi_cell(
        PAGE_TEXT_W, 8, title, new_x=XPos.LMARGIN, new_y=YPos.NEXT
    )
    pdf.set_font("Helvetica", "", 11)
    pdf.set_text_color(40, 40, 40)


def add_bullets(pdf: PDF, items: list[str]):
    pdf.set_font("Helvetica", "", 10)
    for line in items:
        pdf.multi_cell(
            PAGE_TEXT_W,
            6,
            f"  - {line}",
            new_x=XPos.LMARGIN,
            new_y=YPos.NEXT,
        )


def main():
    out = Path(__file__).resolve().parent / "Funcionalidades_App_Alimentacao_Exercicios.pdf"

    pdf = PDF()
    pdf.set_margins(10, 10, 10)
    pdf.alias_nb_pages()
    pdf.set_auto_page_break(auto=True, margin=18)
    pdf.add_page()

    pdf.set_font("Helvetica", "B", 20)
    pdf.set_text_color(20, 20, 20)
    pdf.multi_cell(
        PAGE_TEXT_W,
        10,
        "Funcionalidades do aplicativo",
        new_x=XPos.LMARGIN,
        new_y=YPos.NEXT,
    )
    pdf.ln(2)
    pdf.set_font("Helvetica", "", 12)
    pdf.set_text_color(60, 60, 60)
    pdf.multi_cell(
        PAGE_TEXT_W,
        7,
        "Acompanhamento de alimentação e exercícios físicos - "
        "classificação em Essenciais (MVP), Avançadas e Futuras melhorias.",
        new_x=XPos.LMARGIN,
        new_y=YPos.NEXT,
    )
    pdf.ln(6)

    essential = [
        "Cadastro / login - conta com email ou provedor social (mínimo viável para backup e multi-dispositivo).",
        "Perfil físico - idade, sexo, altura, peso atual, nível de atividade (para estimar necessidades).",
        "Objetivo - perder, ganhar ou manter peso; ritmo ou data-alvo simples.",
        "Meta calórica diária - cálculo automático + ajuste manual opcional.",
        "Diário alimentar - adicionar itens com calorias; idealmente macros básicos (P/C/G).",
        "Base de alimentos - busca + itens recentes/favoritos (pode começar pequena).",
        "Registro de peso - data + valor + lista/gráfico simples ao longo do tempo.",
        "Registro de exercício - tipo, duração; gasto calórico estimado ou campo manual.",
        "Resumo do dia - calorias consumidas vs meta, exercício, saldo aproximado.",
        "Histórico por dia - ver dias anteriores no diário.",
        "Privacidade mínima - política, termos, dados sensíveis tratados com cuidado.",
    ]

    advanced = [
        "Código de barras e porções - leitura + ajuste de quantidade.",
        "Receitas e refeições salvas - montar um prato uma vez e reutilizar.",
        "Relatórios - semanas/meses: médias, tendência de peso, consistência.",
        "Metas de macros - alvos separados para proteína, carbo, gordura.",
        "Lembretes - refeição, peso, treino, hidratação (notificações).",
        "Hidratação - registro de água com meta diária.",
        "Integrações - Apple Health / Google Fit / wearables (passos, treinos).",
        "Modo offline - uso sem rede com sincronização depois.",
        "Múltiplos dias-alvo - ex.: mais calorias em dia de treino.",
        "Exportação de dados - CSV/JSON para o usuário levar os dados.",
        "Onboarding guiado - tutorial curto e primeira meta configurada em minutos.",
    ]

    future = [
        "IA - sugestão de refeições, o que couber no restante do dia, foto de prato (com limitações e disclaimer).",
        "Comunidade moderada - grupos, desafios, feed (moderação e saúde mental são críticos).",
        "Coach humano - integração com nutricionista/personal (marketplace ou B2B).",
        "Planejamento de cardápio - lista de compras ligada ao plano da semana.",
        "Medidas corporais / fotos de progresso - com criptografia, controle fino de privacidade.",
        "Restrições e preferências - vegetariano, alergias, jejum intermitente (regras no diário).",
        "Gamificação equilibrada - sequências e conquistas sem incentivar comportamentos extremos.",
        "Multi-idioma e localização - alimentos e unidades por região.",
        "Modo familiar / vários perfis - um aparelho, contas separadas (com consentimento).",
        "API pública / Zapier - para power users e integrações próprias.",
    ]

    add_section_title(pdf, "Essenciais (MVP)")
    add_bullets(pdf, essential)

    add_section_title(pdf, "Avançadas")
    add_bullets(pdf, advanced)

    add_section_title(pdf, "Futuras melhorias")
    add_bullets(pdf, future)

    pdf.output(str(out))
    print(f"PDF criado: {out}")


if __name__ == "__main__":
    main()
