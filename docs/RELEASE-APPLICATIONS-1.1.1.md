# Pelham Applications 1.1.1 — legibilidad de los PDF

Actualizar únicamente el plugin Pelham Applications con `dist/pelham-applications-1.1.1.zip`. Volver a descargar el PDF desde el expediente: los archivos descargados anteriormente no cambian. No se ha desplegado en Hostinger.

Los datos de reclamaciones utilizan texto de 12 puntos, con reducción limitada a 10 puntos para campos estrechos. La fecha se presenta como MM/DD/YYYY. Los textos se alinean con las líneas originales; pérdidas y daños respetan el ancho de cada renglón. Si no caben, se remite al suplemento, que conserva el valor íntegro. También se ajustan líneas base en crédito y condiciones.

Las firmas se recortan únicamente en una copia temporal para eliminar márgenes blancos y transparentes; su imagen original, revisión firmada y huella no se modifican. La copia se encaja en el espacio de firma sin invadir los campos vecinos. La calidad sigue limitada por la captura original. El recorte requiere GD; si no está disponible o la imagen supera cuatro millones de píxeles, se utiliza la imagen original.

Validación en WordPress local: exportación de los cuatro documentos, revisión de páginas renderizadas, fecha en su casilla, descripciones largas conservadas, firma diminuta sobre lienzo amplio y verificación de que su original permanece intacto. Comprobados borradores, revisiones históricas y rechazo de descargas anónimas. Evidencia con datos ficticios en `docs/audit/pdf-1.1.1/` y pruebas generales en `docs/audit/documents-1.1.0/`.
