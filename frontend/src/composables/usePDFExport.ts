import html2pdf from 'html2pdf.js'

export function usePDFExport() {
  const exportToPDF = async (elementId: string, filename?: string) => {
    const element = document.getElementById(elementId)
    if (!element) {
      console.error(`Elemento com ID "${elementId}" não encontrado`)
      throw new Error(`Elemento não encontrado: ${elementId}`)
    }

    const defaultFilename = filename || `visao-executiva-${new Date().toISOString().split('T')[0]}.pdf`

    // Configurações do PDF
    const opt = {
      margin: [10, 10, 10, 10] as [number, number, number, number],
      filename: defaultFilename,
      image: { type: 'jpeg' as const, quality: 0.98 },
      html2canvas: { 
        scale: 2,
        useCORS: true,
        logging: false,
        backgroundColor: '#ffffff'
      },
      jsPDF: { 
        unit: 'mm', 
        format: 'a4', 
        orientation: 'landscape' as const
      },
      pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    }

    try {
      await html2pdf().set(opt).from(element).save()
    } catch (error) {
      console.error('Erro ao gerar PDF:', error)
      throw error
    }
  }

  return {
    exportToPDF
  }
}

