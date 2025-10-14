#!/usr/bin/env python3
"""
Kivy Test Application
This script tests if Kivy is properly installed and can run on this PC
"""

from kivy.app import App
from kivy.uix.boxlayout import BoxLayout
from kivy.uix.label import Label
from kivy.uix.button import Button
from kivy.core.window import Window

class TestKivyApp(App):
    def build(self):
        # Set window size
        Window.size = (400, 300)

        # Create main layout
        layout = BoxLayout(orientation='vertical', padding=20, spacing=10)

        # Add title label
        title = Label(
            text='Kivy is Working!',
            font_size='24sp',
            size_hint=(1, 0.3),
            color=(0.2, 0.6, 0.8, 1)
        )

        # Add info label
        info = Label(
            text='If you can see this window,\nKivy is successfully installed\nand running on your PC.',
            font_size='16sp',
            size_hint=(1, 0.5),
            halign='center'
        )

        # Add close button
        close_btn = Button(
            text='Close Window',
            size_hint=(1, 0.2),
            background_color=(0.8, 0.2, 0.2, 1)
        )
        close_btn.bind(on_press=self.close_app)

        # Add widgets to layout
        layout.add_widget(title)
        layout.add_widget(info)
        layout.add_widget(close_btn)

        return layout

    def close_app(self, instance):
        """Close the application"""
        App.get_running_app().stop()

if __name__ == '__main__':
    TestKivyApp().run()
