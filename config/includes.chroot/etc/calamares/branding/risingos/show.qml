/* RisingOS — Calamares Slideshow
 * slideshowAPI: 2  ← DEBE coincidir con branding.desc
 *
 * Fix item 2: con API 2 Calamares llama a onActivate() / onLeave()
 * y el slideshow se controla desde QML con un Timer.
 * La pantalla en blanco ocurría porque el QML usaba API 1 sin declararlo.
 *
 * Las 5 slides usan imagen (estilo isométrico 3D consistente, paleta
 * RisingOS) en lugar de íconos de texto/emoji, para mantener jerarquía
 * visual pareja entre todas las diapositivas.
 */

import QtQuick 2.15
import QtQuick.Controls 2.15

Item {
    id: root
    width:  800
    height: 520

    // ── Calamares Slideshow API 2 ──────────────────────────────
    // Estas funciones son llamadas por Calamares al mostrar/ocultar el slideshow
    function onActivate()   { timer.start() }
    function onLeave()      { timer.stop()  }

    // ── Slides ──────────────────────────────────────────────────
    property int currentSlide: 0
    property var slides: [
        {
            title:  "Bienvenido a RisingOS",
            body:   "Sistema Operativo GNU/Linux, desarrollado para potenciar\ntu hardware, con el poder y la estabilidad de Debian.",
            image:  "rising-arrow-hero.png"
        },
        {
            title:  "Estable por diseño",
            body:   "Basado en Debian 13 Trixie y XFCE 4.20:\nliviano, confiable, sin sorpresas.",
            image:  "escritorio.png"
        },
        {
            title:  "Arrancá con todo resuelto",
            body:   "Rising Welcome te guía desde el primer arranque:\nconfigurá tu sistema, instalá apps y conocé las novedades.",
            image:  "rising-welcome-robot.png"
        },
        {
            title:  "IA local, sin depender de internet",
            body:   "Ollama, Qwen y Open WebUI instalados con un clic.\nCorre en tu propia máquina.",
            image:  "rising-ia-robot.png"
        },
        {
            title:  "Con los pies en Latinoamérica,\ny la mirada en el mundo",
            body:   "Soporte en español, repositorio propio, y decisiones\npensadas en los usuarios de la región. Pero como esto\nes Linux, sencillamente es mundial.",
            image:  "rising-globe-hero.png"
        }
    ]

    // ── Timer de avance automático ─────────────────────────────
    Timer {
        id: timer
        interval: 5000
        repeat:   true
        onTriggered: {
            currentSlide = (currentSlide + 1) % slides.length
        }
    }

    // ── Fondo ───────────────────────────────────────────────────
    Rectangle {
        anchors.fill: parent
        color: "#0c1221"
    }

    // ── Imagen de fondo del slide activo ───────────────────────
    Image {
        id: slideImage
        anchors.fill: parent
        source:    slides[currentSlide].image
        fillMode:  Image.PreserveAspectCrop
        clip:      true

        Behavior on opacity { NumberAnimation { duration: 400 } }
    }

    // ── Gradiente oscuro inferior, para legibilidad del texto ──
    Rectangle {
        anchors {
            left:   parent.left
            right:  parent.right
            bottom: parent.bottom
        }
        height: parent.height * 0.48

        gradient: Gradient {
            GradientStop { position: 0.0; color: "#000c1221" }
            GradientStop { position: 0.55; color: "#cc0c1221" }
            GradientStop { position: 1.0;  color: "#0c1221" }
        }
    }

    // ── Texto superpuesto, anclado abajo ───────────────────────
    Column {
        anchors {
            left:         parent.left
            right:        parent.right
            bottom:       parent.bottom
            bottomMargin: 46
            leftMargin:   parent.width * 0.06
            rightMargin:  parent.width * 0.06
        }
        spacing: 10

        Text {
            text:           slides[currentSlide].title
            color:          "#ff6a3d"
            font.pixelSize: 25
            font.bold:      true
            anchors.horizontalCenter: parent.horizontalCenter
            horizontalAlignment: Text.AlignHCenter
            width:          parent.width
            wrapMode:       Text.WordWrap

            Behavior on opacity { NumberAnimation { duration: 400 } }
        }

        Text {
            text:           slides[currentSlide].body
            color:          "#e8eaf0"
            font.pixelSize: 14
            wrapMode:       Text.WordWrap
            anchors.horizontalCenter: parent.horizontalCenter
            horizontalAlignment: Text.AlignHCenter
            width:          parent.width
        }
    }

    // ── Indicadores de posición ────────────────────────────────
    Row {
        anchors {
            horizontalCenter: parent.horizontalCenter
            bottom:           parent.bottom
            bottomMargin:     16
        }
        spacing: 10

        Repeater {
            model: slides.length
            Rectangle {
                width:  index === currentSlide ? 22 : 8
                height: 8
                radius: 4
                color:  index === currentSlide ? "#ff6a3d" : "#1a2340"

                Behavior on width { NumberAnimation { duration: 300 } }
                Behavior on color { ColorAnimation  { duration: 300 } }
            }
        }
    }
}
