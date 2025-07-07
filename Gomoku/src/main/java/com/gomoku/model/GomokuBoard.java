package com.gomoku.model;

public class GomokuBoard {
    private final int SIZE = 15;
    private final int WIN_CONDITION = 5;
    private int[][] board;

    public GomokuBoard() {
        board = new int[SIZE][SIZE];
    }

    //Plasare miscare
    public boolean placeMove(int row, int col, int player) {
        if (row < 0 || row >= SIZE || col < 0 || col >= SIZE || board[row][col] != 0) {
            return false;
        }
        board[row][col] = player;
        return true;
    }

    //Verificare directii castig
    public boolean checkWin(int row, int col, int player) {
        return checkDirection(row, col, player, 1, 0) // orizontal
                || checkDirection(row, col, player, 0, 1) // vertical
                || checkDirection(row, col, player, 1, 1) // diagonala
                || checkDirection(row, col, player, 1, -1); // diagonala /
    }

    //Verificare in amblele sensuri (pozitivi si negativ)
    private boolean checkDirection(int row, int col, int player, int dx, int dy) {
        int count = 1;

        int r = row - dx, c = col - dy;
        while (isValid(r, c) && board[r][c] == player) {
            count++;
            r -= dx;
            c -= dy;
        }

        r = row + dx;
        c = col + dy;
        while (isValid(r, c) && board[r][c] == player) {
            count++;
            r += dx;
            c += dy;
        }

        return count >= WIN_CONDITION;
    }

    //Verifica daca o piesa esta valida pe tabla
    private boolean isValid(int row, int col) {
        return row >= 0 && row < SIZE && col >= 0 && col < SIZE;
    }

    public void resetBoard() {
        board = new int[SIZE][SIZE];
    }

    public int getCell(int row, int col) {
        if (!isValid(row, col)) return -1;
        return board[row][col];
    }

    public int getSize() {
        return SIZE;
    }
}
