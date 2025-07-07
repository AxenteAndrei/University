package com.gomoku.view;

import com.gomoku.model.GomokuBoard;
import com.gomoku.view.StartMenu;

import javax.swing.*;
import java.awt.*;
import java.io.FileWriter;
import java.io.IOException;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class GomokuGUI extends JFrame {
    private JButton[][] buttons;
    private GomokuBoard board;
    private int currentPlayer = 1;
    private String player1Name;
    private String player2Name;
    private JPanel boardPanel;
    private JLabel turnLabel;
    private JLabel casuteLabel;
    private Color player1Color;
    private Color player2Color;

    private int countPlayer1 = 0;
    private int countPlayer2 = 0;

    public GomokuGUI(String player1Name, String player2Name, Color player1Color, Color player2Color) {
        this.player1Name = player1Name;
        this.player2Name = player2Name;
        this.player1Color = player1Color;
        this.player2Color = player2Color;

        board = new GomokuBoard();
        buttons = new JButton[board.getSize()][board.getSize()];

        //Titlu
        setTitle("Gomoku - " + player1Name + " vs " + player2Name);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setSize(880, 920);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        //Panou pentru butoane si scor
        add(createTopPanel(), BorderLayout.NORTH);
        boardPanel = new JPanel();
        initializeBoard();
        add(boardPanel, BorderLayout.CENTER);

        setVisible(true);
    }

    private JPanel createTopPanel() {
        JPanel topPanel = new JPanel(new GridLayout(2, 1));

        //Label pentru afisare tura
        JPanel infoPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 20, 5));
        turnLabel = new JLabel("Turn: " + player1Name);
        turnLabel.setFont(new Font(Font.SANS_SERIF, Font.BOLD, 14));
        infoPanel.add(turnLabel);

        //Buton Reset
        JButton resetButton = new JButton("Reset Game");
        resetButton.addActionListener(e -> resetGame());
        infoPanel.add(resetButton);

        //Buton Back2Menu
        JButton backButton = new JButton("Back to Menu");
        backButton.addActionListener(e -> {
            dispose(); // Închide fereastra de joc
            new StartMenu(); // Deschide meniul de start
        });
        infoPanel.add(backButton);

        topPanel.add(infoPanel);

        //Panou piese plasate
        JPanel casutePanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        casuteLabel = new JLabel();
        casuteLabel.setFont(new Font(Font.SANS_SERIF, Font.PLAIN, 13));
        updateCasuteLabel();
        casutePanel.add(casuteLabel);
        topPanel.add(casutePanel);

        return topPanel;
    }

    //Actualizeaza piesele plasate
    private void updateCasuteLabel() {
        casuteLabel.setText(player1Name + ": " + countPlayer1 + "   |   " +
                player2Name + ": " + countPlayer2);
    }

    //Initializare tabla
    private void initializeBoard() {
        int size = board.getSize();
        boardPanel.removeAll();
        boardPanel.setLayout(new GridLayout(size + 1, size + 1));

        //Adaugare coordonate casute (1 - 15, A - O)
        for (int row = 0; row <= size; row++) {
            for (int col = 0; col <= size; col++) {
                if (row == 0 && col == 0) {
                    boardPanel.add(new JLabel(""));
                } else if (row == 0) {
                    JLabel label = new JLabel(String.valueOf((char) ('A' + col - 1)), SwingConstants.CENTER);
                    label.setFont(new Font(Font.SANS_SERIF, Font.BOLD, 13));
                    boardPanel.add(label);
                } else if (col == 0) {
                    JLabel label = new JLabel(String.valueOf(row), SwingConstants.CENTER);
                    label.setFont(new Font(Font.SANS_SERIF, Font.BOLD, 13));
                    boardPanel.add(label);
                } else {
                    JButton button = new JButton();
                    int actualRow = row - 1;
                    int actualCol = col - 1;
                    String coord = String.valueOf((char) ('A' + actualCol)) + (actualRow + 1);

                    button.setText(coord);
                    button.setFont(new Font(Font.MONOSPACED, Font.PLAIN, 10));
                    button.setForeground(new Color(180, 180, 180));
                    button.setBackground(Color.WHITE);
                    button.setMargin(new Insets(0, 0, 0, 0));

                    int finalRow = actualRow;
                    int finalCol = actualCol;

                    button.addActionListener(e -> {
                        if (board.placeMove(finalRow, finalCol, currentPlayer)) {
                            button.setText(""); // Sterge coordonata
                            button.setForeground(Color.BLACK);
                            button.setBackground(currentPlayer == 1 ? player1Color : player2Color);

                            if (currentPlayer == 1) countPlayer1++;
                            else countPlayer2++;
                            updateCasuteLabel();

                            //Check daca jucatorul a castigat
                            if (board.checkWin(finalRow, finalCol, currentPlayer)) {
                                String winner = currentPlayer == 1 ? player1Name : player2Name;
                                String loser = currentPlayer == 1 ? player2Name : player1Name;
                                Color winnerColor = currentPlayer == 1 ? player1Color : player2Color;
                                int casute = currentPlayer == 1 ? countPlayer1 : countPlayer2;
                                String winCoord = String.valueOf((char) ('A' + finalCol)) + (finalRow + 1);

                                //Save game rezults + Resetare tabla
                                saveGameResult(winner, loser, winnerColor, casute, winCoord);

                                JOptionPane.showMessageDialog(this,
                                        "Player " + winner + " wins at " + winCoord + "!");
                                resetGame();
                                return;
                            }

                            //Schimba tura
                            currentPlayer = currentPlayer == 1 ? 2 : 1;
                            turnLabel.setText("Turn: " + (currentPlayer == 1 ? player1Name : player2Name));
                        }
                    });

                    buttons[actualRow][actualCol] = button;
                    boardPanel.add(button);
                }
            }
        }

        boardPanel.revalidate();
        boardPanel.repaint();
    }

    //Functie reset game
    private void resetGame() {
        board.resetBoard();
        currentPlayer = 1;
        countPlayer1 = 0;
        countPlayer2 = 0;
        updateCasuteLabel();
        turnLabel.setText("Turn: " + player1Name);

        for (int i = 0; i < board.getSize(); i++) {
            for (int j = 0; j < board.getSize(); j++) {
                String coord = String.valueOf((char) ('A' + j)) + (i + 1);
                buttons[i][j].setText(coord);
                buttons[i][j].setForeground(new Color(180, 180, 180));
                buttons[i][j].setBackground(Color.WHITE);
            }
        }
    }

    //Functie save score + introducere fisier
    private void saveGameResult(String winnerName, String opponentName, Color winnerColor, int casute, String coordFinala) {
        String timestamp = LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm"));
        String culoare = colorToName(winnerColor);
        String linie = winnerName + " vs " + opponentName +
                " | Culoare: " + culoare +
                " | Casute: " + casute +
                " | Castig la: " + coordFinala +
                " | Data: " + timestamp;

        //Introducerea in fisier
        try (FileWriter fw = new FileWriter("game_results.txt", true)) {
            fw.write(linie + "\n");
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    //Convertire culoare in string
    private String colorToName(Color color) {
        if (color.equals(Color.RED)) return "Rosu";
        if (color.equals(Color.BLUE)) return "Albastru";
        if (color.equals(Color.GREEN)) return "Verde";
        if (color.equals(Color.YELLOW)) return "Galben";
        if (color.equals(Color.ORANGE)) return "Portocaliu";
        if (color.equals(Color.PINK)) return "Roz";
        return "Necunoscut";
    }
}