/* RisingOS — Calamares Slideshow
 * slideshowAPI: 2  ← DEBE coincidir con branding.desc
 *
 * Fix item 2: con API 2 Calamares llama a onActivate() / onLeave()
 * y el slideshow se controla desde QML con un Timer.
 * La pantalla en blanco ocurría porque el QML usaba API 1 sin declararlo.
 */

import QtQuick 2.15
import QtQuick.Controls 2.15

Item {
    id: root
    width:  800
    height: 520

    // ── Calamares Slideshow API 2 ─────────────────────────────────────────
    // Estas funciones son llamadas por Calamares al mostrar/ocultar el slideshow
    function onActivate()  { timer.start() }
    function onLeave()     { timer.stop()  }

    // ── Slides ────────────────────────────────────────────────────────────
    property int currentSlide: 0
    property var slides: [
        {
            title:    "Bienvenido a RisingOS",
            body:     "Una distribución Linux moderna para Latinoamérica,\nbasada en Debian 13 Trixie con XFCE 4.20.",
            icon:     "🚀"
        },
        {
            title:    "Potenciado por IA",
            body:     "RisingOS incluye Rising IA: instalá Ollama, Qwen y\nOpen WebUI con un solo clic, sin internet extra.",
            icon:     "🤖"
        },
        {
            title:    "Herramientas para tu negocio",
            body:     "Rising Welcome te guía desde el primer arranque:\nconfigurá tu sistema, instalá apps y conocé las novedades.",
            icon:     "🛠"
        },
        {
            title:    "Comunidad latinoamericana",
            body:     "Soporte en español, repositorios propios y actualizaciones\npensadas para la región.",
            icon:     "🌎"
        }
    ]

    // ── Timer de avance automático ────────────────────────────────────────
    Timer {
        id: timer
        interval: 5000
        repeat:   true
        onTriggered: {
            currentSlide = (currentSlide + 1) % slides.length
        }
    }

    // ── Fondo ─────────────────────────────────────────────────────────────
    Rectangle {
        anchors.fill: parent
        color: "#0c1221"
    }

    // ── Contenido del slide activo ────────────────────────────────────────
    Column {
        anchors.centerIn: parent
        spacing: 24
        width: parent.width * 0.75

        Text {
            text:            slides[currentSlide].icon
            font.pixelSize:  64
            anchors.horizontalCenter: parent.horizontalCenter

            Behavior on opacity { NumberAnimation { duration: 400 } }
        }

        Text {
            text:            slides[currentSlide].title
            color:           "#ff6a3d"
            font.pixelSize:  26
            font.bold:       true
            anchors.horizontalCenter: parent.horizontalCenter
            horizontalAlignment: Text.AlignHCenter

            Behavior on opacity { NumberAnimation { duration: 400 } }
        }

        Text {
            text:            slides[currentSlide].body
            color:           "#e8eaf0"
            font.pixelSize:  15
            wrapMode:        Text.WordWrap
            anchors.horizontalCenter: parent.horizontalCenter
            horizontalAlignment: Text.AlignHCenter
            width:           parent.width
        }
    }

    // ── Indicadores de posición ───────────────────────────────────────────
    Row {
        anchors {
            horizontalCenter: parent.horizontalCenter
            bottom:           parent.bottom
            bottomMargin:     28
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

    // ── Transición suave entre slides ─────────────────────────────────────
    // Animamos opacity del Column al cambiar currentSlide
    states: State {
        name: "changing"
    }
}
