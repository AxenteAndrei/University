package com.gomoku.view;

import javax.swing.*;
import java.awt.*;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public class StartMenu extends JFrame {
    private static final Map<String, Color> COLOR_MAP = new LinkedHashMap<>();

    //Initializare culori din ComboBox
    static {
        COLOR_MAP.put("R", Color.RED);
        COLOR_MAP.put("B", Color.BLUE);
        COLOR_MAP.put("G", Color.GREEN);
        COLOR_MAP.put("Y", Color.YELLOW);
        COLOR_MAP.put("O", Color.ORANGE);
        COLOR_MAP.put("P", Color.PINK);
    }

    public StartMenu() {

        //Titlu
        setTitle("Gomoku - Start Menu");
        setSize(600, 200);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout(10, 10));

        JLabel titleLabel = new JLabel("GOMOKU", SwingConstants.CENTER);
        titleLabel.setFont(new Font(Font.SERIF, Font.BOLD, 36));
        add(titleLabel, BorderLayout.NORTH);

        //Text box user names & ComboBox Culori
        JPanel centerPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 10, 10));

        JTextField player1Field = new JTextField("Player 1", 10);
        JComboBox<String> color1Box = new JComboBox<>(COLOR_MAP.keySet().toArray(new String[0]));
        color1Box.setPreferredSize(new Dimension(40, 25));

        JLabel vsLabel = new JLabel("VS");
        vsLabel.setFont(new Font(Font.SANS_SERIF, Font.BOLD, 20));

        JTextField player2Field = new JTextField("Player 2", 10);
        JComboBox<String> color2Box = new JComboBox<>(COLOR_MAP.keySet().toArray(new String[0]));
        color2Box.setPreferredSize(new Dimension(40, 25));
        color2Box.setSelectedIndex(1);

        //UI
        centerPanel.add(new JLabel("Player 1:"));
        centerPanel.add(player1Field);
        centerPanel.add(color1Box);
        centerPanel.add(vsLabel);
        centerPanel.add(color2Box);
        centerPanel.add(new JLabel("Player 2:"));
        centerPanel.add(player2Field);

        add(centerPanel, BorderLayout.CENTER);

        //Butoane
        JPanel buttonPanel = new JPanel();
        JButton startButton = new JButton("Start Game");
        JButton viewHistoryButton = new JButton("Vezi istoric jocuri");
        JButton exitButton = new JButton("Exit");

        buttonPanel.add(startButton);
        buttonPanel.add(viewHistoryButton);
        buttonPanel.add(exitButton);
        add(buttonPanel, BorderLayout.SOUTH);

        startButton.addActionListener(e -> {
            String player1 = player1Field.getText().trim();
            String player2 = player2Field.getText().trim();
            Color color1 = COLOR_MAP.get((String) color1Box.getSelectedItem());
            Color color2 = COLOR_MAP.get((String) color2Box.getSelectedItem());

            new GomokuGUI(player1, player2, color1, color2);
            dispose();
        });

        viewHistoryButton.addActionListener(e -> showGameHistory());
        exitButton.addActionListener(e -> System.exit(0));

        setVisible(true);
    }


    //Show game history
    private void showGameHistory() {
        JFrame historyFrame = new JFrame("Istoric jocuri");
        historyFrame.setSize(600, 400);
        historyFrame.setLocationRelativeTo(this);
        historyFrame.setLayout(new BorderLayout());

        JTextArea textArea = new JTextArea();
        textArea.setEditable(false);
        JScrollPane scrollPane = new JScrollPane(textArea);

        try {
            Path filePath = Paths.get("game_results.txt");
            if (Files.exists(filePath)) {
                List<String> lines = Files.readAllLines(filePath);
                for (String line : lines) {
                    textArea.append(line + "\n");
                }
            } else {
                textArea.setText("Nu exista niciun istoric salvat inca.");
            }
        } catch (IOException e) {
            textArea.setText("Eroare la citirea fisierului.");
            e.printStackTrace();
        }

        historyFrame.add(scrollPane, BorderLayout.CENTER);
        historyFrame.setVisible(true);
    }
}