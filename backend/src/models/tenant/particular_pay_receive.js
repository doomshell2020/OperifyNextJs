const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('particular_pay_receive', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    particular_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    recive_amount: {
      type: DataTypes.DECIMAL(10,2),
      allowNull: false
    },
    recive_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    total_amount: {
      type: DataTypes.DECIMAL(10,2),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    invoice_file: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'particular_pay_receive',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
